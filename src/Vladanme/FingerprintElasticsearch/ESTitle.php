<?php

namespace Vladanme\FingerprintElasticsearch;

use Vladanme\Fingerprint\Title as FingerprintTitle;

class ESTitle extends FingerprintElasticsearch
{
    protected $filter_syn_name_es = 'fp_title_syn';
    protected $filter_rem_name_es = 'fp_title_rem';
    protected $analyzer_fp_name_es = 'fp_title_analyzer';

    public function __construct()
    {
        $fingerprintTitle = new FingerprintTitle();
        $fingerprintElasticsearchType = new FingerprintElasticsearchType($fingerprintTitle);
        parent::__construct($fingerprintElasticsearchType);
    }

    public function analyzerES()
    {
        $filter_synonym_name = $this->getFilterSynNameES();
        $filter_removal_name = $this->getFilterRemNameES();
        $analyzer_fp_name = $this->getAnalyzerFpNameES();
        $analyzers[$analyzer_fp_name] = [
          'type'      => 'custom',
          'tokenizer' => 'standard',
          'filter'    => [
            'lowercase',
            'asciifolding',
            $filter_synonym_name,
            $filter_removal_name,
            'fingerprint'
          ]
        ];
        return $analyzers;
    }

    public function filterES()
    {
        $filter_synonym_name = $this->getFilterSynNameES();
        $filter_removal_name = $this->getFilterRemNameES();
        $syn = $this->getAllSynonymsES();
        $rem = $this->getAllRemovalsES();
        $filters = [
          $filter_synonym_name => [
            'type'     => 'synonym',
            'synonyms' => $syn
          ],
          $filter_removal_name => [
            'type'      => 'stop',
            'stopwords' => $rem
          ],
          'remove_numbers'     => [
            'type'        => 'pattern_replace',
            'pattern'     => '\\p{Digit}',
            'replacement' => '',
          ],
        ];
        return $filters;
    }

    public function propES()
    {
        return [
          'type'         => 'keyword',
          'ignore_above' => 256,
          'fields'       => [
            'fp'              => [
              'type'     => 'text',
              'analyzer' => $this->analyzer_fp_name_es,
            ],
            'puncteater'      => [
              'type'     => 'text',
              'analyzer' => 'puncteater',
            ],
            'puncteater2gram' => [
              'type'     => 'text',
              'analyzer' => 'puncteater2gram',
            ],
            'puncteater3gram' => [
              'type'     => 'text',
              'analyzer' => 'puncteater3gram',
            ],
          ]

        ];
    }

}
