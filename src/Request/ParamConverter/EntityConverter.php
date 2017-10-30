<?php
/*
 * The MIT License
 *
 * Copyright 2017 Rob Treacy <robert.treacy@thesalegroup.co.uk>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace TheSaleGroup\RestormBundle\Request\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use TheSaleGroup\Restorm\EntityManager;
use TheSaleGroup\Restorm\Mapping\Exception\UnknownEntityException;

/**
 * Description of EntityConverter
 *
 * @author Rob Treacy <robert.treacy@thesalegroup.co.uk>
 */
class EntityConverter implements ParamConverterInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $identifierValue = $this->getIdentifierValue($request, $configuration->getName());
        
        if(!$identifierValue) {
            return false;
        }
        
        $entity = $this->entityManager->getRepository($configuration->getClass())->findOne($identifierValue);
        
        $request->attributes->set($configuration->getName(), $entity);
        
        return true;
    }

    public function supports(ParamConverter $configuration): bool
    {
        if(!$configuration->getClass()) {
            return false;
        }
        
        try {
            $this->entityManager->getEntityMappingRegister()->getEntityMapping($configuration->getClass());
        } catch (UnknownEntityException $e) {
            return false;
        }
        
        return true;
    }
    
    protected function getIdentifierValue(Request $request, $name)
    {
        return $request->attributes->get($name);
    }
}
