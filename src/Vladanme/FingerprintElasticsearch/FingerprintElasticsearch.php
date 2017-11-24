<?php

namespace Vladanme\FingerprintElasticsearch;

use Vladanme\Fingerprint\Fingerprint;

class FingerprintElasticsearch extends Fingerprint {
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

  protected function getFilterSynNameES() {
    return $this->filter_syn_name_es;
  }

  protected function getFilterRemNameES() {
    return $this->filter_rem_name_es;
  }

  protected function getAnalyzerFpNameES() {
    return $this->analyzer_fp_name_es;
  }

  protected function getAllSynonymsES() {
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
      }
      else {
        $es_syn[] = $key . ' => ' . $value;
      }
      $old_value = $value;
    }
    // @todo Create a path? Won't really work because that file should be
    // @todo located on elasticsearch server, not on staging/production server.
    return $es_syn;
  }

  protected function getAllRemovalsES() {
    $rem = $this->getAllRemovals();
    // @todo Create a path? Won't really work because that file should be
    // @todo located on elasticsearch server, not on staging/production server.
    return $rem;
  }

  public function analyzerES() {
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

  public function tokenizerES() {
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

  public function filterES() {
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
