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

namespace TheSaleGroup\RestormBundle\Validator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use TheSaleGroup\Restorm\EntityManager;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use TheSaleGroup\Restorm\Entity\EntityMetadata;

/**
 * Description of UniqueEntityValidator
 *
 * @author Rob Treacy <robert.treacy@thesalegroup.co.uk>
 */
class UniqueEntityValidator extends ConstraintValidator
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueEntity) {
            throw new UnexpectedTypeException($constraint, UniqueEntity::class);
        }

        if (!is_array($constraint->fields) && !is_string($constraint->fields)) {
            throw new UnexpectedTypeException($constraint->fields, 'array');
        }

        if (!is_string($constraint->class)) {
            throw new UnexpectedTypeException($constraint->class, 'string');
        }

        $fields = (array) $constraint->fields;
        $entityClass = $constraint->class;

        if (0 === count($fields)) {
            throw new ConstraintDefinitionException('At least one field has to be specified.');
        }

        if (!is_a($value, $entityClass, true)) {
            throw new ConstraintDefinitionException('Entity is not of given class.');
        }

        if (null === $value) {
            return;
        }

        $entityMetadata = $this->entityManager->getEntityMetadataRegister()->getEntityMetadata($value) ?: $this->createEntityMetadata($value, $entityClass);

        $filter = array();
        foreach($fields as $field) {
            $filter[$field] = $entityMetadata->getPropertyValue($field);
        }

        $existingEntities = $this->entityManager->getRepository($entityClass)->find($filter, 1, 2);

        if(count($existingEntities) === 0) {
            return;
        }

        foreach($existingEntities as $existingEntity) {
            if($existingEntity === $value) {
                continue;
            }

            $this->context->buildViolation($constraint->message)
                ->addViolation();

            break;
        }
    }

    private function createEntityMetadata($entity, string $entityClass): EntityMetadata
    {
        $entityMapping = $this->entityManager->getEntityMappingRegister()->getEntityMapping($entityClass);
        $entityMetadata = new EntityMetadata($entity, $entityMapping);

        $this->entityManager->getEntityMetadataRegister()->addEntityMetadata($entityMetadata);

        return $entityMetadata;
    }
}
