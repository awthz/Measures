<?php
/**
 * This class contains the model for the gr_pdfs table.
 *
 * @author Luvly Isaac Skelton <isaac@luvly.co.nz>
 * @package Classes\Models\GR
 * @since 15/03/2019
 */

namespace Classes\Models\GR;

use Illuminate\Database\Eloquent\Model;

class PDF extends Model
{
    protected $connection = 'default';
    
    protected $table = 'gr_pdfs';

    public $timestamps = false;

    protected $dates = ['created'];

    protected $casts = [
        'id'        => 'int',
        'filter_id' => 'int',
        'status'    => 'bool',
    ];

    const TYPE_GENERATED = 'generated';

    const TYPE_UPLOAD = 'upload';

    public function save(array $options = [])
    {
        if (!$this->created) {
            $this->created = (new \DateTime)->format('Y-m-d H:i:s');
        }
        parent::save($options);
    }
}
