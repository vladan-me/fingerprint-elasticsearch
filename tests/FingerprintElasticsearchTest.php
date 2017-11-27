<?php

namespace tests;

use Vladanme\Fingerprint\FingerprintType;
use Vladanme\FingerprintElasticsearch\ESCity;
use Vladanme\FingerprintElasticsearch\ESTitle;
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
        // First three are coming from extended city specific removals
        // and 'the' is from English stopwords that make sense for cities.
        // Even though 'a', 'of' and 'on' are also stopwords they are not
        // included by default in standard fingerprint algorithm but for
        // Elasticsearch index it doesn't cost us anything to include all of them.
        $expectedResult = ['a', 'of', 'on', 'the'];

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
              'bch => beach',
              'cty => city',
              'e => east',
              'fls => falls',
              'ft => fort',
              'hgts,hts => heights',
              'hls => hills',
              'mt => mount',
              'n => north',
              'st => saint',
              's => south',
              'vly => valley',
              'vlg => village',
              'w => west',
            ],
          ],
          'fp_city_rem' => [
            'type'      => 'stop',
            'stopwords' => [
              'a',
              'of',
              'on',
              'the',
            ],
          ],
        ];
        $expectedProp = [
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
        ];

        $this->assertEquals($expectedAnalyzer, $analyzer);
        $this->assertEquals($expectedFilter, $filter);
        $this->assertEquals($expectedProp, $prop);
    }

    public function testTitleSynonymsRemovals()
    {
        $ESTitle = new ESTitle();

        $filter = $ESTitle->filterES();

        $expectedFilterSynonyms = [
          'adj => adjunct',
          'administrato,administr,admin,administrat,administrati,administratio,administra,admi,administ,administration,admini,adminis => administrator',
          'ast,asst,assistan,assi,assistants,assista,assist,assis => assistant',
          'assoc,assc => associate',
          'chair => chairman',
          'ceo => chief executive officer',
          'cfo => chief financial officer',
          'cmo => chief marketing officer',
          'coo => chief operating officer',
          'cto => chief technology officer',
          'clin => clinical',
          'ctr => control',
          'directo,directors,direc,dir,dire => director',
          'educ => education',
          'engin,engi,eng,enginee,engineers => engineer',
          'enviro,environment,environ,env,envir => environmental',
          'exe,execut,executives,execu,exec,executiv,executi => executive',
          'hum,huma => human',
          'hr,h r,human res => human resources',
          'inform,info,informat,infor,informa,informati,informatio,inf,informations => information',
          'it,i t => information technology',
          'instr => instructor',
          'jr => junior',
          'man,mgr,manag,manage,ma,mngr,mana,managers => manager',
          'offi,officers,offic,off => officer',
          'operation,operatio,ope,opera,operat,operati,oper => operations',
          'owners => owner',
          'presiden,presid,preside,presi,pres => president',
          'pro => professional',
          'rep => representative',
          'resou,reso,resourc,resour,resource => resources',
          'sls => sales',
          'sci => science',
          'snr,sr => senior',
          'servi,srvs,srv,servic,service,srvcs,srvc,ser,serv => services',
          'stf => staff',
          'supp,supt,spt => support',
          'system,syste,syst,sys => systems',
          'technolog,techno,technol,techn,technolo,technologies,tech => technology',
          'us,usa => united states',
          'v p,vp => vice president',
        ];
        $expectedFilterRemovals = [
          'united states',
          'st',
          'nd',
          'rd',
          'th',
          'first',
          'second',
          'third',
          'fourth',
          'fifth',
          'sixth',
          'seventh',
          'eighth',
          'ninth',
          'grade',
          'global',
          'front',
          'end',
          'web',
          'top',
          'i',
          'ii',
          'iii',
          'iv',
          'europe',
          'asia',
          'pacific',
          'latin',
          'canada',
          'an',
          'and',
          'at',
          'for',
          'in',
          'of',
          'or',
          'the',
          'to',
        ];
        $filterSynonyms = $filter['fp_title_syn']['synonyms'];
        $filterRemovals = $filter['fp_title_rem']['stopwords'];

        $this->assertEquals($expectedFilterSynonyms, $filterSynonyms);
        $this->assertEquals($expectedFilterRemovals, $filterRemovals);
    }
}
