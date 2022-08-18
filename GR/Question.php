<?php

namespace Classes\Models\GR;

use Classes\Reports\TCPDFCustom;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $connection = 'default';

    protected $table = 'gr_question';

    public $timestamps = false;

    protected $guarded = ['id'];

    public function addMeta(string $key, $value)
    {
        if (is_object($value) || is_array($value)) {
            $value = json_encode($value);
        } elseif (!is_string($value) && !is_int($value) && !is_bool($value)) {
            throw new \InvalidArgumentException('Only objects, arrays, strings, ints and bools can be set as the value.');
        }

        $meta = new QuestionMeta;
        $meta->question_id = $this->id;
        $meta->key = $key;
        $meta->value = $value;
        $meta->save();

        return $this;
    }

    public function meta()
    {
        return $this->hasMany(QuestionMeta::class, 'question_id');
    }

    public function getMeta(bool $get_keys = false, bool $process = false)
    {
        $meta = $this->meta()->get();

        if ($get_keys) {
            $tmp = [];
            foreach ($meta as $m) {
                if ($process) {
                    $decode = json_decode($m->value);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $m->value = $decode;
                    }
                }

                $tmp[$m->key] = $m->value;
            }

            $meta = $tmp;
        }

        return $meta;
    }

    public function answers()
    {
        return $this->hasOne(Answer::class, 'question_id');
    }

    public function getAnswers()
    {
        return $this->answers->get();
    }

    public function getAnswer(int $filter_id)
    {
        $answer = $this->answers()
            ->select('answer')
            ->where('filter_id', '=', $filter_id)
            ->first();

        if ($answer) {
            $answer = $answer->getAnswer();
        }

        return $answer;
    }

    public function getAnswerStripped(int $filter_id)
    {
        $answer = $this->answers()
            ->select('answer')
            ->where('filter_id', '=', $filter_id)
            ->first();

        if ($answer) {
            $answer = $answer->getAnswerStripped();
        }

        return $answer;
    }    

    public function title()
    {
        return $this->belongsTo(Title::class, 'parent');
    }

    public function getTitle()
    {
        return $this->title()->first();
    }

    public function recursiveDelete()
    {
        $metas = $title->getMeta();
        if ($metas) {
            foreach ($metas as $meta) {
                $meta->forceDelete();
            }
        }

        $answers = $this->getAnswers();
        if ($answers) {
            foreach ($answers as $answer) {
                $answer->forceDelete();
            }
        }

        $this->forceDelete();
    }

    public function renderPdf(TCPDFCustom &$pdf)
    {
    }

    public function renderWeb(int $filter_id, Title $title, array $classes = [], array $merge = [])
    {
        $meta = $this->getMeta();
        $tpl = '';
        $data = [];
        $extra_data = null;
        $answer = $this->getAnswer($filter_id);

        $tmp = [];
        if (!empty($meta)) {
            foreach ($meta as $m) {
                $m->key = strtolower($m->key);
                $decode = json_decode($m->value);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $m->value = $decode;
                }

                $tmp[$m->key] = $m->value;

                if ($m->key === 'template') {
                    $tpl = $m->value;
                } elseif ($m->key === 'type') {
                    $m->value = strtolower($m->value);

                    if ($m->value === 'textarea') {
                        $tpl = 'components.TextArea';
                        $data = [
                            'hasBtn'        => false,
                            'question_id'   => $this->id,
                        ];
                    } elseif ($m->value === 'number') {
                        $tpl = 'components.NumberInput';
                        $data = [
                            'type'      => 'number',
                            'id'        => $title->id
                        ];
                    } elseif ($m->value === 'dropdown') {
                        $tpl = 'components.DropDown';
                    } elseif ($m->value === 'dropdownmultiselect') {
                        $tpl = 'components.DropDownMultiSelect';
                    }
                } elseif ($m->key === 'line_graph') {
                    $tpl = 'components.LineGraph';
                    $data = [
                        'meta'      => $m,
                        'instance'  => $m->class,
                    ];
                } elseif ($m->key === 'top_bar_graph') {
                    $tpl = 'components.TopBarGraph';
                    $data = [
                        'meta'      => $m,
                        'instance'  => $m->class,
                    ];
                } elseif ($m->key === 'bar_graph') {
                    $tpl = 'components.BarGraph';
                    $data = [
                        'meta'      => $m,
                        'instance'  => $m->class,
                    ];
                } elseif ($m->key === 'horozontal_bar_graph') {
                    $tpl = 'components.HorozontalBarGraph';
                    $data = [
                        'meta'      => $m,
                        'instance'  => $m->class,
                    ];
                } elseif ($m->key === 'list') {
                    // TODO: Lists.
                    continue;
                } elseif ($m->key === 'disablednumber') {
                    if (class_exists($m->value->class) && method_exists($m->value->class, $m->value->function)) {
                        try {
                            $tmp = $m->value->class;
                            $tmp = new $tmp;
                            $disablednumber = $tmp->{$meta->value->function}();
                        } catch (\Throwable $e) {
                            //
                        }

                        $tpl = 'components.NumberInput';
                        $data = [
                            'type'          => 'number',
                            'id'            => $title->id,
                            'question'      => $this->question,
                            'placeholder'   => '00',
                            'disabled'      => 'disabled',
                        ];
                    }
                } elseif ($m->key === 'pie_graph') {
                    $tpl = 'components.PieGraph';
                    $data = ['meta' => $m];
                } elseif ($m->key === 'list_title') {
                    // TODO: List title.
                    continue;
                } elseif ($m->key === 'check_list') {
                    $tpl = 'components.CheckListPanel';
                    $data = [
                        'meta'      => $m,
                        'instance'  => $m->class,
                        'checklist' => $this,
                    ];
                } elseif ($m->key === 'spacing') {
                    $tpl = 'components.Spacer';
                    $data = ['meta' => $m];
                } elseif ($m->key === 'hbar_stats') {
                    $tpl = 'components.HBarStats';
                    $data = [
                        'meta'      => $m,
                        'instance'  => $m->class,
                    ];
                } elseif ($m->key === 'timeline') {
                    // TODO: Timeline.
                    continue;
                } elseif ($m->key === 'desc_tbl') {
                    // TODO: Descriptor table.
                    continue;
                } elseif ($m->key === 'domain_bargraph') {
                    // TODO: Domain bargraph.
                    continue;
                } elseif ($m->key === 'neet_srv_graph') {
                    // TODO: NEET Service Graph.
                    continue;
                } elseif ($m->key === 'data') {
                    $extra_data = ['data' => $m->value];
                }
            }
        }
        $meta = $tmp;

        $fulldata = [
            'filter_id'     => $filter_id,
            'filter_meta'   => Filter::find($filter_id)->getMeta(),
            'title'         => $title,
            'model'         => $this,
            'question'      => $this->question,
            'meta'          => $meta,
            'classes'       => $classes,
            'order'         => $this->order,
            'answer'        => $answer,
        ];

        if (is_array($data)) {
            $fulldata = array_merge($fulldata, $data);
        }

        if (is_array($extra_data)) {
            $fulldata = array_merge($fulldata, $extra_data);
        }

        if (is_array($merge) && !empty($merge)) {
            $fulldata = array_merge($fulldata, $merge);
        }

        return render_blade($tpl, $fulldata);
    }
}
