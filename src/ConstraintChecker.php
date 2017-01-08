<?php

namespace Mnabialek\LaravelHandyRequest;

class ConstraintChecker
{
    /**
     * Verify whether field can be matched to field constraints
     *
     * @param string $fullKey
     * @param array $constraints
     *
     * @return bool
     */
    public function canBeMatchedToFieldsConstraints($fullKey, array $constraints)
    {
        foreach ($constraints as $constraint) {
            if ($this->fieldMatchesConstraint($fullKey, $constraint)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verify whether field with given key matches given constraint
     *
     * @param string $fullKey
     * @param string $constraint
     *
     * @return bool
     */
    public function fieldMatchesConstraint($fullKey, $constraint)
    {
        if (ends_with($constraint, '.**')) {
            // 1st replace all dots into PCRE dot character
            $regex = str_replace('.', '\.', $constraint);
            // then replace all single asterisk into any character expression (except dot)
            $regex = preg_replace('/(?<!\*)\*(?!\*)/', '(?>[^\.])+', $regex);
            // finally replace ending double asterisk into any character expression
            $regex = '/^(' . str_replace_last('\.**', '\..*', $regex) . ')$/';

            if (preg_match($regex, $fullKey)) {
                return true;
            }
        } else {
            // replace all dots into PCRE dot character and all asterisk into any character
            // expression (except dot)
            $regex = '/^(' . str_replace(['.', '*'], ['\.', '(?>[^\.])+'], $constraint) . ')$/';
            if (preg_match($regex, $fullKey)) {
                return true;
            }
        }

        return false;
    }
}
