<?php

namespace Classes\Models\GR;

use Classes\Reports\TCPDFCustom;
use Illuminate\Database\Eloquent\Model;

class Title extends Model
{
    protected $connection = 'default';

    protected $table = 'gr_title';

    public $timestamps = false;

    public $incrementing = false;

    protected $guarded = [];

    public function createQuestions(callable $callback)
    {
        $callback($this->section, $this->id);
    }

    public function addMeta(string $key, $value)
    {
        $allowed = 'objects, arrays, booleans and strings';

        if (is_object($value) || is_array($value)) {
            $value = json_encode($value);
        } elseif (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        } elseif (!is_string($value)) {
            throw new \InvalidArgumentException("Only $allowed can be set as the meta value.");
        }

        $meta = new TitleMeta;
        $meta->title_id = $this->id;
        $meta->key = $key;
        $meta->value = $value;
        $meta->save();

        return $this;
    }

    public function removeMeta(string $key)
    {
        TitleMeta::where('title_id', $this->id)
            ->where('key', $key)
            ->delete();
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'parent')
            ->orderBy('order', 'asc');
    }

    public function getQuestions()
    {
        $questions = $this->questions()->get();
        return $questions;
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section');
    }

    public function getSection()
    {
        return $this->section()->first();
    }

    public function meta()
    {
        return $this->hasMany(TitleMeta::class, 'title_id');
    }

    public function getMeta(bool $with_keys = false, bool $process = false)
    {
        $meta = $this->meta()->get();

        if ($with_keys) {
            $tmp = [];
            foreach ($meta as $m) {
                if ($process) {
                    $decode = json_decode($m->value);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $m->value = $decode;
                    }
                }

                $tmp[$m->key] = $m;
            }

            $meta = $tmp;
        }

        return $meta;
    }

    public function state()
    {
        return $this->hasOne(QuestionState::class, 'title_id');
    }

    public function getState($fid)
    {
        $state = $this->state()->where('rfid', $fid)->first();

        if (!$state) {
            return (object) ['hidden' => false];
        }

        return $state;
    }

    public function recursiveDelete($fid)
    {
        $questions = $title->getQuestions();
        if ($questions) {
            foreach ($questions as $question) {
                $question->recursiveDelete();
            }
        }

        $metas = $title->getMeta();
        if ($metas) {
            foreach ($metas as $meta) {
                $meta->forceDelete();
            }
        }

        $this->getState($fid)->forceDelete();
        $this->forceDelete();
    }

    public function renderPdf(TCPDFCustom &$pdf)
    {
    }

    public function renderWeb(int $filter_id)
    {
        $meta = $this->getMeta();
        $tpl = 'BlueCurvePanel';
        $docupload = false;
        $classes = [
            'Assessment'    => new \Classes\Assessment,
            'Report'        => new \Classes\Report,
            'Indicator'     => new \Classes\Indicator,
            'Assessor'      => new \Classes\Assessor,
        ];

        $tmp = [];
        if (!empty($meta)) {
            foreach ($meta as $m) {
                $m->key = strtolower($m->key);
                $tmp[$m->key] = $m->value;

                if ($m->key === 'template') {
                    $tpl = $m->value;
                } elseif ($m->key === 'document-upload') {
                    $decode = json_decode(stripslashes($m->value));
                    if (json_last_error() !== JSON_ERROR_NONE && $m->value !== 'true') {
                        continue;
                    }

                    if ($m->value === 'true') {
                        $docupload = (object) [
                            'id'            => "for-{$this->id}",
                            // Function or boolean.
                            'disabled'      => false,
                            'hide_empty'    => false,
                        ];
                    } elseif (is_object($docupload)) {
                        $docupload = $m->value;
                    }
                } elseif ($m->key === 'classes') {
                    $decode = json_decode(stripslashes($m->value));
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decode)) {
                        $classes = $decode;
                    }

                    // Create each class.
                    foreach ($classes as $idx => $class) {
                        if (!class_exists($class)) {
                            continue;
                        }

                        try {
                            $classes[$idx] = new $class;
                        } catch (\Throwable $e) {
                            continue;
                        }
                    }
                }
            }
        }
        $meta = $tmp;

        return render_blade($tpl, [
            'title'     => $this,
            'docupload' => $docupload,
            'meta'      => $meta,
            'classes'   => $classes,
            'filter_id' => $filter_id,
        ]);
    }
}
