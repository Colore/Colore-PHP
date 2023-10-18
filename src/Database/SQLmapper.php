<?php

namespace Colore\Database;

use Colore\Logger;

define('Colore\Database\STRING_WRAPPER', '( %s )');
define('Colore\Database\STRING_ASSIGNMENT', '%s = :%s');

class SQLmapper
{
    /**
     * @return (array|string)[]|null
     *
     * @psalm-return array{statement: string, arguments: array<string, mixed>}|null
     */
    public static function generateSQL(array $statementInfo): array|null
    {
        // If statementInfo is not an array, or if it is missing required fields, then bail
        if (!is_array($statementInfo) || !isset($statementInfo['action']) || !isset($statementInfo['table'])) {
            return null;
        }

        // Check for required fields on select, update, replace and insert.
        if (
            preg_match('/(select|update|replace|insert)/', $statementInfo['action'])
            && (!is_array($statementInfo['fields']) || count($statementInfo['fields']) == 0)
        ) {
            return null;
        }

        // Initialize statement variable
        $sqlResult = [];
        $sqlResult['statement'] = [];
        $sqlResult['arguments'] = [];

        Logger::debug('Generating sql for action: %s', $statementInfo['action']);

        // Start statement based on
        switch ($statementInfo['action']) {
            case 'insert':
                // INSERT
                $sqlResult['statement'][0] = 'INSERT INTO';
                $sqlResult['statement'][1] = $statementInfo['table'];

                // field names
                $fieldNames = [];

                reset($statementInfo['fields']);

                // loop over fields and generate appropriate arrays
                foreach ($statementInfo['fields'] as $fieldKey => $fieldVal) {
                    // Generate the descriptor
                    $fieldDescriptor = sprintf(':%s', $fieldKey);

                    // Add the values to both the statement and arguments
                    $fieldNames[] = $fieldKey;
                    $fieldDescriptors[] = $fieldDescriptor;
                    $sqlResult['arguments'][$fieldDescriptor] = $fieldVal;
                }

                $sqlResult['statement'][] = sprintf(\Colore\Database\STRING_WRAPPER, join(',', $fieldNames));
                $sqlResult['statement'][] = 'VALUES';
                $sqlResult['statement'][] = sprintf(\Colore\Database\STRING_WRAPPER, join(',', $fieldDescriptors));

                // Done
                break;

            case 'replace':
                // REPLACE
                $sqlResult['statement'][0] = 'REPLACE INTO';
                $sqlResult['statement'][1] = $statementInfo['table'];

                // field names
                $fieldNames = [];
                $fieldReferences = [];

                reset($statementInfo['fields']);

                // loop over fields and generate appropriate arrays
                foreach ($statementInfo['fields'] as $fieldKey => $fieldVal) {
                    // Generate the descriptor
                    $fieldDescriptor = sprintf(':%s', $fieldKey);

                    // Add the values to both the statement and arguments
                    $fieldNames[] = $fieldKey;
                    $fieldDescriptors[] = $fieldDescriptor;
                    $sqlResult['arguments'][$fieldDescriptor] = $fieldVal;
                }

                $sqlResult['statement'][] = sprintf(\Colore\Database\STRING_WRAPPER, join(',', $fieldNames));
                $sqlResult['statement'][] = 'VALUES';
                $sqlResult['statement'][] = sprintf(\Colore\Database\STRING_WRAPPER, join(',', $fieldDescriptors));

                // Done
                break;

            case 'update':
                // UPDATE
                $sqlResult['statement'][] = 'UPDATE';
                $sqlResult['statement'][] = $statementInfo['table'];
                $sqlResult['statement'][] = 'SET';

                $fieldIteratorLoopCount = 0;

                foreach ($statementInfo['fields'] as $fieldKey => $fieldVal) {
                    // If we move ahead past the first field, insert a comma to separate arguments
                    if ($fieldIteratorLoopCount > 0) {
                        $sqlResult['statement'][] = ',';
                    }
                    $fieldIteratorLoopCount++;

                    // Generate the descriptor
                    $fieldDescriptor = sprintf(':%s', $fieldKey);

                    // Add the values to both the statement and arguments
                    $sqlResult['statement'][] .= sprintf('%s = %s', $fieldKey, $fieldDescriptor);
                    $sqlResult['arguments'][$fieldDescriptor] = $fieldVal;
                }

                // If criteria has been set, then add it
                if (isset($statementInfo['criteria'])) {
                    $sqlResult['statement'][] = sprintf('WHERE');

                    $fieldIteratorLoopCount = 0;

                    foreach ($statementInfo['criteria'] as $fieldKey => $fieldVal) {
                        // If we move ahead past the first field, insert a comma to separate arguments
                        if ($fieldIteratorLoopCount > 0) {
                            $sqlResult['statement'][] = 'AND';
                        }
                        $fieldIteratorLoopCount++;

                        $fieldDescriptor = sprintf(':%s', $fieldKey);
                        $sqlResult['statement'][] = sprintf(\Colore\Database\STRING_ASSIGNMENT, $fieldKey, $fieldKey);
                        $sqlResult['arguments'][$fieldDescriptor] = $fieldVal;
                    }
                }

                break;

            case 'select':
                // SELECT
                $sqlResult['statement'][] = 'SELECT';


                $fieldIteratorLoopCount = 0;

                foreach ($statementInfo['fields'] as $fieldKey => $fieldVal) {
                    // If we move ahead past the first field, insert a comma to separate arguments
                    if ($fieldIteratorLoopCount > 0) {
                        $sqlResult['statement'][] = ',';
                    }
                    $fieldIteratorLoopCount++;

                    // Add the values to both the statement and arguments
                    if ($fieldVal) {
                        $sqlResult['statement'][] .= sprintf('%s', $fieldKey);
                    }
                }

                $sqlResult['statement'][] = 'FROM';
                $sqlResult['statement'][] = $statementInfo['table'];

                // If criteria has been set, then add it
                if (isset($statementInfo['criteria'])) {
                    $sqlResult['statement'][] = sprintf('WHERE');

                    $fieldIteratorLoopCount = 0;

                    foreach ($statementInfo['criteria'] as $fieldKey => $fieldVal) {
                        // If we move ahead past the first field, insert a comma to separate arguments
                        if ($fieldIteratorLoopCount > 0) {
                            $sqlResult['statement'][] = 'AND';
                        }
                        $fieldIteratorLoopCount++;

                        $fieldDescriptor = sprintf(':%s', $fieldKey);
                        $sqlResult['statement'][] = sprintf(\Colore\Database\STRING_ASSIGNMENT, $fieldKey, $fieldKey);
                        $sqlResult['arguments'][$fieldDescriptor] = $fieldVal;
                    }
                }

                // If order has been set, then add it
                if (isset($statementInfo['order']) && count($statementInfo['order']) > 0) {
                    $sqlResult['statement'][] = sprintf('ORDER BY');

                    $criteriaLoopState = 0;

                    foreach ($statementInfo['order'] as $fieldKey => $fieldOrder) {
                        if ($criteriaLoopState > 0) {
                            $sqlResult['statement'][] = ',';
                        } else {
                            $criteriaLoopState = 1;
                        }

                        $sqlResult['statement'][] = sprintf('%s %s', $fieldKey, $fieldOrder);
                    }
                }

                break;

            case 'delete':
                // DELETE
                Logger::debug('Generating SQL for delete on: %s', $statementInfo['table']);

                $sqlResult['statement'][] = 'DELETE FROM';
                $sqlResult['statement'][] = $statementInfo['table'];

                // If criteria has been set, then add it
                if (isset($statementInfo['criteria']) && count($statementInfo['criteria']) > 0) {
                    $sqlResult['statement'][] = sprintf('WHERE');

                    $criteriaLoopState = 0;

                    foreach ($statementInfo['criteria'] as $fieldKey => $fieldVal) {
                        if ($criteriaLoopState > 0) {
                            $sqlResult['statement'][] = 'AND';
                        } else {
                            $criteriaLoopState = 1;
                        }

                        $fieldDescriptor = sprintf(':%s', $fieldKey);
                        $sqlResult['statement'][] = sprintf(\Colore\Database\STRING_ASSIGNMENT, $fieldKey, $fieldKey);
                        $sqlResult['arguments'][$fieldDescriptor] = $fieldVal;
                    }
                }

                break;

            default:
                // Default is to break
                Logger::debug('Unknown action for: %s', print_r($statementInfo, 1));

                return null;
        }

        // Join to add it all together with spaces
        $sqlResult['statement'] = join(' ', $sqlResult['statement']);

        Logger::debug('Query: %s', print_r($sqlResult['statement'], 1));
        Logger::debug('Arguments: %s',   print_r($sqlResult['arguments'], 1));

        // Return the combined result set
        return $sqlResult;
    }
}
