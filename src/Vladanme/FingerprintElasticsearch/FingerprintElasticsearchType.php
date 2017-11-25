<?php

namespace Vladanme\FingerprintElasticsearch;

use Vladanme\Fingerprint\FingerprintType;

class FingerprintElasticsearchType
{
    /**
     * All (greedy) class specific synonyms/removals and synonym=>removal arrays.
     *
     * @var array
     */
    protected $syn = [];
    protected $rem = [];
    protected $syn_rem = [];

    public function getSyn()
    {
        return $this->syn;
    }

    public function getRem()
    {
        return $this->rem;
    }

    public function getSynRem()
    {
        return $this->syn_rem;
    }

    public function __construct(FingerprintType $fingerprintType)
    {
        $this->syn = $fingerprintType->retrieveAllSynonyms();
        $this->rem = $fingerprintType->retrieveAllRemovals();
        $this->syn_rem = $fingerprintType->retrieveAllSynonymsAndRemovals();
    }
}
