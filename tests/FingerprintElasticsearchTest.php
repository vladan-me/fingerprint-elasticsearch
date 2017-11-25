<?php

namespace tests;

use Vladanme\Fingerprint\FingerprintType;
use Vladanme\FingerprintElasticsearch\ESCity;
use Vladanme\FingerprintElasticsearch\FingerprintElasticsearch;
use Vladanme\FingerprintElasticsearch\FingerprintElasticsearchType;

class FingerprintElasticsearchTest extends \PHPUnit_Framework_TestCase
{

    public function testInit()
    {
        $expectedResult = [];

        $fingerprintType = new FingerprintType();
        $fingerprintElasticsearchType = new FingerprintElasticsearchType($fingerprintType);
        $fingerprintElasticsearch = new FingerprintElasticsearch($fingerprintElasticsearchType);

        $actualResult = $fingerprintElasticsearch->getAllSynonyms();

        $this->assertEquals($expectedResult, $actualResult);

    }

    public function testCityRem()
    {
        $expectedResult = ['a', 'of', 'on'];

        $ESCity = new ESCity();

        $actualResult = $ESCity->getAllRemovals();

        $this->assertEquals($expectedResult, $actualResult);

    }

    public function testCityFunc()
    {
        $ESCity = new ESCity();

        $analyzer = $ESCity->analyzerES();
        $filter = $ESCity->filterES();
        $prop = $ESCity->propES();

        $expectedAnalyzer = [
          'fp_city_analyzer' => [
            'type'      => 'custom',
            'tokenizer' => 'standard',
            'filter'    => [
              'lowercase',
              'asciifolding',
              'fp_city_syn',
              'fp_city_rem',
              'fingerprint',
            ],
          ],
        ];
        $expectedFilter = [
          'fp_city_syn' => [
            'type'     => 'synonym',
            'synonyms' => [
              'afb => air force base',
              'cty => city',
              'fls => falls',
                // Notice that it should merge two
                // or more synonyms into one mapping.
              'hts,hgts => heights',
              'hls => hills',
              'vly => valley',
              'vlg => village',
            ],
          ],
          'fp_city_rem' => [
            'type'      => 'stop',
            'stopwords' => [
              'a',
              'of',
              'on',
            ],
          ],
        ];
        $expectedProp = [
          [
            'type'         => 'keyword',
            'ignore_above' => 256,
            'fields'       => [
              'fp'              => [
                'type'     => 'text',
                'analyzer' => 'fp_city_analyzer',
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
            ],
          ]
        ];

        $this->assertEquals($expectedAnalyzer, $analyzer);
        $this->assertEquals($expectedFilter, $filter);
        $this->assertEquals($expectedProp, $prop);
    }
}
