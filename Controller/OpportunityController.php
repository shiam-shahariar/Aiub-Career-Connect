<?php
class OpportunityController
{
    private DataRepository $repository;

    public function __construct(DataRepository $repository)
    {
        $this->repository = $repository;
    }

    public function search(array $filters): array
    {
        $keyword = trim(strtolower((string) ($filters['keyword'] ?? '')));
        $type = trim((string) ($filters['type'] ?? ''));
        $department = trim((string) ($filters['department'] ?? ''));

        $results = [];

        foreach ($this->repository->getOpportunities() as $opportunity) {
            if ($opportunity['status'] !== 'published' || $opportunity['deadline'] < date('Y-m-d')) {
                continue;
            }

            $matchesKeyword = $keyword === ''
                || stripos($opportunity['title'], $keyword) !== false
                || stripos($opportunity['company'], $keyword) !== false
                || stripos($opportunity['description'], $keyword) !== false;

            $matchesType = $type === '' || strtolower($opportunity['type']) === strtolower($type);
            $matchesDepartment = $department === '' || strtolower($opportunity['department']) === strtolower($department);

            if ($matchesKeyword && $matchesType && $matchesDepartment) {
                $results[] = $opportunity;
            }
        }

        return ['success' => true, 'results' => $results, 'count' => count($results)];
    }
}
