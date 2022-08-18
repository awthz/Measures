<?php

namespace Classes\Models\GR;

use Classes\Reports\TCPDFCustom;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;

class Section extends Model
{
    protected $connection = 'default';

    protected $table = 'gr_section';

    public $timestamps = false;

    protected $guarded = ['id'];

    public function createTitles(callable $callback)
    {
        $callback($this->id);
    }

    public function titles()
    {
        return $this->hasMany(Title::class, 'section')
            ->orderBy('order', 'asc');
    }

    public function getTitles()
    {
        return $this->titles()->get();
    }

    public function recurviveDelete()
    {
        $titles = $this->getTitles();
        foreach ($titles as $title) {
            $title->recurviveDelete();
        }

        $this->forceDelete();
    }

    public function renderSection(int $filter_id, TCPDFCustom &$pdf = null)
    {
        if ($pdf) {
            $this->renderPdf($filter_id, $pdf);
        } else {
            $this->renderWeb($filter_id);
        }
    }

    public function renderPdf(int $filter_id, TCPDFCustom &$pdf)
    {
        if ($this->title) {
            $pdf->writeSectionTitle($this->title);
        }

        if ($this->description) {
            $pdf->writeSectionDescription($this->description);
        }

        $titles = $this->getTitles();
        if (!empty($titles)) {
            foreach ($titles as $title) {
                $title->renderPdf($filter_id);
            }
        }
    }

    public function renderWeb(int $filter_id)
    {
        // Validate render.
        $yar = DB::table('yomm_assessment_report')->where('filter_id', $filter_id)->first();
        $filter = \Classes\Models\GR\Filter::find($filter_id);
        $utility = new \utility;

        if ($yar) {
            if ($yar->region_id != $utility->getRegionID()) {
                response('Permission denied.', 403);
            }
        } elseif ($filter) {
            if ($filter->user_id != get_current_user_id()) {
                response('Permission denied.', 403);
            }
        }

        $titles = $this->getTitles();
        $html = '';

        // $html .= render_blade($this->section_start, [
        //     'section'   => $this,
        //     'filter_id' => $filter_id,
        // ]);

        if (!empty($titles)) {
            foreach ($titles as $title) {
                $html .= $title->renderWeb($filter_id);
            }
        }

        // $html .= render_blade($this->section_end, [
        //     'section'   => $section,
        //     'filter_id' => $filter_id,
        // ]);

        return $html;
    }
}
