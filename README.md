Fingerprint Elasticsearch
=========================

This is a package that prepares Elasticsearch analyzers, filters
and tokenizers to work with custom implementation of fingerprint algorithm.
It is basically an improved version of [fingerprint - official Elasticsearch documentation](https://www.elastic.co/guide/en/elasticsearch/reference/current/analysis-fingerprint-analyzer.html)
The difference is that custom analyzer that is created includes 
synonyms filter making the final combination something like this:

* lowercase
* asciifolding
* fp_X_syn (synonyms for specific type X)
* fp_X_rem (removals for specific type X)
* fingerprint

Available types are City, Company, Street and Title.
That means, whenever you need to store cities, companies,
street addresses or titles you likely need this analyzer that will help
you sort them out. Otherwise, you'll end up with messy data.

Use cases
---------
* Having complete Elasticsearch fingerprint analyzer
* Combined with [fingerprint](https://www.github.com/vladan-me/fingerprint)
makes creation and testing fingerprint easier.

Documentation
-------------

City analyzer/filter/property can be get like this:

```php
        $ESCity = new ESCity();

        $analyzer = $ESCity->analyzerES();
        $filter = $ESCity->filterES();
        $prop = $ESCity->propES();

```

That creates analyzer with specific filters:

```php
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
   ]
```

It is using same synonyms and removals from depending project. 

And specific city property:
```php
   'type'         => 'keyword',
   'ignore_above' => 256,
   'fields'       => [
       'fp' => [
           'type'     => 'text',
           'analyzer' => 'fp_city_analyzer',
       ],
```

Please look at tests for more examples.

System Requirements
-------

You need **PHP >= 5.4.0**.
Even though there's no Elasticsearch dependency,
fingerprint token filter is available from Elasticsearch version 5.0
Depending on [fingerprint](https://www.github.com/vladan-me/fingerprint) for synonyms/removals.

Install
-------

Install `fingerprint-elasticsearch` using Composer.

```
$ composer require vladan-me/fingerprint-elasticsearch
```

Additional Notes
----------------

This package contains additional analyzers
(punctuation removal, 2gram and 3gram with punctuation) that might be
useful depending on use case, but likely should be optional.

Contributing
-------

Contributions are welcome and will be fully credited. Please see [CONTRIBUTING](.github/CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

License
-------

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
