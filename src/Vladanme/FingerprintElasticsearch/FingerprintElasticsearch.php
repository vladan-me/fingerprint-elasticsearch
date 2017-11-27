<?php

namespace Vladanme\FingerprintElasticsearch;

class FingerprintElasticsearch
{
    /**
     * Filter synonym name for ES.
     *
     * @var string
     */
    protected $filter_syn_name_es = '';
    /**
     * Filter removal name for ES.
     *
     * @var string
     */
    protected $filter_rem_name_es = '';
    /**
     * Fingerprint analyzer name for ES.
     *
     * @var string
     */
    protected $analyzer_fp_name_es = '';
    /**
     * Elasticsearch related methods.
     */

    protected $syn = [];
    protected $syn_rem = [];
    protected $rem = [];

    public function __construct(
      FingerprintElasticsearchType $fingerprintElasticsearchType
    ) {
        $this->syn = $fingerprintElasticsearchType->getSyn();
        $this->syn_rem = $fingerprintElasticsearchType->getSynRem();
        $this->rem = $fingerprintElasticsearchType->getRem();
    }

    /**
     * @param string $filter_syn_name_es
     */
    public function setFilterSynNameEs($filter_syn_name_es)
    {
        $this->filter_syn_name_es = $filter_syn_name_es;
    }

    /**
     * @param string $filter_rem_name_es
     */
    public function setFilterRemNameEs($filter_rem_name_es)
    {
        $this->filter_rem_name_es = $filter_rem_name_es;
    }
    /**
     * @param string $analyzer_fp_name_es
     */
    public function setAnalyzerFpNameEs($analyzer_fp_name_es)
    {
        $this->analyzer_fp_name_es = $analyzer_fp_name_es;
    }

    public function getAllSynonyms()
    {
        return array_merge($this->syn, $this->syn_rem);
    }

    public function getAllRemovals()
    {
        $removals = array_merge($this->syn_rem, $this->rem);
        // Remove keys from this array, there's no need.
        return array_values(array_unique($removals));
    }

    protected function getFilterSynNameES()
    {
        return $this->filter_syn_name_es;
    }

    protected function getFilterRemNameES()
    {
        return $this->filter_rem_name_es;
    }

    protected function getAnalyzerFpNameES()
    {
        return $this->analyzer_fp_name_es;
    }

    protected function getAllSynonymsES()
    {
        $syn = $this->getAllSynonyms();
        $es_syn = [];
        $old_value = '';
        // Order is important for the following loop.
        asort($syn);
        foreach ($syn as $key => $value) {
            if ($value == $old_value) {
                // Merge with previous.
                $count = count($es_syn);
                $es_syn[$count - 1] = $key . ',' . $es_syn[$count - 1];
            } else {
                $es_syn[] = $key . ' => ' . $value;
            }
            $old_value = $value;
        }
        return $es_syn;
    }

    protected function getAllRemovalsES()
    {
        $rem = $this->getAllRemovals();
        return $rem;
    }

    public function analyzerES()
    {
        $analyzers = [
          'puncteater'      => [
            'type'      => 'custom',
            'tokenizer' => 'keyword',
            'filter'    => [
              'lowercase',
              'asciifolding',
              'remove_punct',
            ],
          ],
          'puncteater2gram' => [
            'tokenizer' => '2gram_tokenizer',
            'filter'    => [
              'lowercase',
              'asciifolding',
            ],
          ],
          'puncteater3gram' => [
            'tokenizer' => '3gram_tokenizer',
            'filter'    => [
              'lowercase',
              'asciifolding',
            ],
          ],
        ];
        return $analyzers;
    }

    public function tokenizerES()
    {
        $tokenizers = [
          '2gram_tokenizer' => [
            'type'        => 'ngram',
            'min_gram'    => 2,
            'max_gram'    => 2,
            'token_chars' => [
              'letter',
              'digit'
            ]
          ],
          '3gram_tokenizer' => [
            'type'        => 'ngram',
            'min_gram'    => 3,
            'max_gram'    => 3,
            'token_chars' => [
              'letter',
              'digit'
            ]
          ]
        ];
        return $tokenizers;
    }

    public function filterES()
    {
        $filters = [
          '2gram'        => [
            'type'        => 'ngram',
            'min_gram'    => 2,
            'max_gram'    => 2,
            'token_chars' => [
              'letter',
              'digit',
            ]
          ],
          'remove_punct' => [
            'type'        => 'pattern_replace',
            'pattern'     => '\\p{Punct}|\\p{Cntrl}|\\p{Space}',
            'replacement' => '',
          ],
        ];
        return $filters;
    }
}
