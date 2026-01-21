<?php

namespace App\Installation\Steps;

use App\Installation\Modules\ModuleRegistry;
use App\Installation\Validators\EnvironmentValidator;

/**
 * Vérification des prérequis système.
 */
class CheckRequirementsStep extends BaseStep
{
    protected string $description = 'Vérification des prérequis système';

    /**
     * Exécute la vérification des prérequis.
     */
    public function execute(ModuleRegistry $moduleRegistry): array
    {
        $this->log('Checking system requirements');

        $validation = EnvironmentValidator::validateComplete();

        if (!$validation['valid']) {
            $errors = [];
            foreach ($validation['results'] as $check => $result) {
                if (!empty($result['errors'])) {
                    $errors = array_merge($errors, $result['errors']);
                }
            }

            return $this->failed($errors);
        }

        $this->log('All requirements met');

        return $this->success('Tous les prérequis sont satisfaits', $validation['results']);
    }
}
