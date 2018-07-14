<?php

namespace Rubix\ML\Classifiers;

use Rubix\ML\Graph\Nodes\Decision;
use InvalidArgumentException;

/**
 * Extra Tree
 *
 * An Extremely Randomized Classification Tree that splits the training set at
 * a random point chosen among the maximum features. Extra Trees are useful in
 * Ensembles such as Random Forest or AdaBoost as the “weak” classifier or they
 * can be used on their own. The strength of Extra Trees are computational
 * efficiency as well as increasing variance of the prediction (if that is
 * desired).
 *
 * @category    Machine Learning
 * @package     Rubix/ML
 * @author      Andrew DalPino
 */
class ExtraTree extends ClassificationTree
{
    /**
     * Randomized algorithm to that choses the split point with the lowest gini
     * impurity among a random selection of $maxFeatures features.
     *
     * @param  array  $data
     * @return \Rubix\ML\Graph\Nodes\Decision
     */
    protected function findBestSplit(array $data) : Decision
    {
        $best = [
            'gini' => INF, 'index' => null, 'value' => null, 'groups' => [],
        ];

        shuffle($this->indices);

        foreach (array_slice($this->indices, 0, $this->maxFeatures) as $index) {
            $value = $data[rand(0, count($data) - 1)][$index];

            $groups = $this->partition($data, $index, $value);

            $gini = $this->calculateGiniImpurity($groups);

            if ($gini < $best['gini']) {
                $best['gini'] = $gini;
                $best['index'] = $index;
                $best['value'] = $value;
                $best['groups'] = $groups;
            }

            if ($gini === 0.0) {
                break 1;
            }
        }

        return new Decision($best['index'], $best['value'],
            $best['gini'], $best['groups']);
    }
}
