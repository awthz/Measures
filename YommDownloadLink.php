<?php

namespace Classes\Models;

use DateTime;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $full_path
 * @property string $filename
 * @property string $code
 * @property int $uses_remaining
 * @property Carbon|null $expired_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property User $user
 * @method static YommDownloadLink|null create(array $array)
 */
class YommDownloadLink extends Model
{
    protected $dates = [
        'created_at',
        'updated_at',
        'expired_at',
    ];

    protected $guarded = ['id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'ID');
    }

    /**
     * Convert the file to an HTML page.
     * @return string
     * @throws Exception
     */
    public function toHTML(): string
    {
        return $this->checkPermissions(function () {
            // Get MIME type and contents.
            $mime = mime_content_type($this->full_path);
            $contents = file_get_contents($this->full_path);
            $b64 = base64_encode($contents);

            $html = '<html><head></head><body>';
            $html .= "
            <style>
                body { margin: 0; padding: 0 }
                * { box-sizing: border-box }
                embed, iframe { width: 100vw; height: 100vh }
                
                #overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    opacity: 0;
                    width: calc(100vw - 20px);
                    height: 100vh;
                }
            </style>
            ";

            if ($mime === 'application/pdf') {
                $html .= "
                <div id='overlay'></div>
                <script>
                function b64toBlob(b64Data, contentType) {
                    let byteCharacters = atob(b64Data);
                    let byteArrays = [];
                    
                    for (let offset = 0; offset < byteCharacters.length; offset += 512) {
                        let slice = byteCharacters.slice(offset, offset + 512);
                        let byteNumbers = new Array(slice.length);
                        
                        for (let i = 0; i < slice.length; i++) {
                            byteNumbers[i] = slice.charCodeAt(i);
                        }
                        
                        let byteArray = new Uint8Array(byteNumbers);
                        byteArrays.push(byteArray);
                    }
                    
                    return new Blob(byteArrays, { type: contentType });
                }
                
                async function showPdf() {
                    const link = URL.createObjectURL(b64toBlob('$b64', '$mime'));
                    const el = document.createElement('iframe');
                    el.src = link + '#toolbar=0&navpanes=0&scrollbar=0&download=0';
                    el.id = 'embeded';
                    document.body.appendChild(el);
                }
                
                document.addEventListener('DOMContentLoaded', showPdf);
                </script>
                ";
            } else {
                // If it is wider than it is taller than scale it to 100vw max rather than vh.
                $html .= "
                <style>
                    img {
                        position: fixed;
                        top: 50%;
                        left: 50%;
                        transform: translate(-50%, -50%);
                        max-width: 99vw;
                        max-height: 99vh;
                        margin: auto;
                    }
                </style>
                ";

                $html .= "<img src=\"data:$mime;base64,$b64\">";
            }

            $html .= "
            <script>
            window.addEventListener('contextmenu', (e) => e.preventDefault());
            
            window.addEventListener('keydown', (event) => {
                if (event.ctrlKey || event.metaKey) {
                    switch (event.key.toLowerCase()) {
                        case 's':
                            event.preventDefault();
                            break;
                    }
                }
            });
            </script>
            ";

            $html .= '</body></html>';

            return $html;
        });
    }

    public function useLink()
    {
        return $this->checkPermissions(function () {
            // Get MIME type.
            $mime = mime_content_type($this->full_path);

            // Present file.
            http_response_code(200);
            header("Content-Type: $mime");
            header('Pragma: no-cache');
            header("Content-Disposition: inline; filename={$this->filename};");
            ob_end_clean();
            readfile($this->full_path);
        });
    }

    /**
     * Check the permissions of this link.
     * @param callable $handler
     * @return mixed
     * @throws Exception
     */
    private function checkPermissions(callable $handler)
    {
        // Verify that the link hasn't expired.
        if ($this->expired_at && Carbon::now() > $this->expired_at) {
            $this->delete();
            http_response_code(403);
            die;
        }

        // Verify that it is this user who is authorized to view this link.
        if ($this->user_id !== get_current_user_id()) {
            http_response_code(403);
            die;
        }

        // Verify that they can still use this link.
        if ($this->uses_remaining < 1) {
            $this->delete();
            http_response_code(403);
            die;
        }

        // Delete all expired links.
        YommDownloadLink::where('expired_at', '<', Carbon::now())->delete();

        // Reduce the uses remaining by one. If it reaches zero, delete it.
        $this->uses_remaining--;
        $this->save();

        // Audit that the user has viewed this.
        YommDownloadLinkAudit::create([
            'user_id' => get_current_user_id(),
            'full_path' => $this->full_path,
        ]);

        $result = $handler();

        if ($this->uses_remaining < 1) {
            $this->delete();
        }

        return $result;
    }

    /**
     * The URL to this file.
     * @param string $full_path
     * @param string $filename
     * @param int $usages
     * @param Carbon|DateTime|string|null
     * @return string
     */
    public static function createLink(string $full_path, string $filename, int $usages = 1, $expired_at = null): string
    {
        // If a link already exists just update it's expire time.
        $link = YommDownloadLink::where('user_id', get_current_user_id())
            ->where('full_path', $full_path)
            ->where('filename', $filename)
            ->where('expired_at', '>=', Carbon::now())
            ->first();

        if ($link) {
            $link->update(['uses_remaining' => $usages, 'expired_at' => $expired_at]);
        } else {
            $link = YommDownloadLink::create([
                'user_id' => get_current_user_id(),
                'code' => self::createRandomCode(),
                'full_path' => $full_path,
                'filename' => $filename,
                'uses_remaining' => $usages,
                'expired_at' => $expired_at,
            ]);
        }

        return sprintf('/view-file?code=%s', $link->code);
    }

    private static function createRandomCode(): string
    {
        $len = 32;
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $cend = strlen($chars);

        do {
            $str = '';

            for ($i = 0; $i < $len; $i++) {
                $str .= $chars[rand(0, $cend)];
            }
        } while (YommDownloadLink::where('code', $str)->exists());

        return $str;
    }
}
