<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Repository\OptionRepository;
use App\Utils\ApiError;
use App\Utils\ApiPayload;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY', statusCode: 401)]
class OptionController extends ApiAbstractController
{
    #[Route('/api/option', name: 'api_option', methods: ['GET'])]
    public function option(
        #[MapQueryParameter]
        string $option,
        OptionRepository $optionRepository
    ): Response {
        if (empty($option))
        {
            return $this->json(ApiPayload::withError(ApiError::BAD_REQUEST));
        }

        return $this->json(
            ApiPayload::new($optionRepository->getOption($option))->setAttribute('option', $option)
        );
    }

    #[Route('/api/options', name: 'api_options', methods: ['GET'])]
    public function options(
        Request $request,
        OptionRepository $optionRepository
    ): Response {
        $badRequest = ApiPayload::withError(ApiError::BAD_REQUEST);

        $options    = $request->query->all()['options'] ?? [];

        if (is_string($options))
        {
            $options = $this->parseOptions($options);
        }

        if ( ! is_array($options) || empty($options))
        {
            return $this->json($badRequest);
        }

        $values     = $keys = [];

        foreach ($options as $name)
        {
            foreach ($this->parseOptions($name) as $opt)
            {
                if ( ! is_string($opt) || ! preg_match('#^[a-z]#i', $opt))
                {
                    return new JsonResponse($badRequest);
                }
                $values[$name] = $optionRepository->getOption($opt);
                $keys[]        = $name;
            }
        }

        return $this->json(
            ApiPayload::new($values)->setAttribute('requested_options', $keys)
        );
    }

    protected function parseOptions(string $name): array
    {
        $options = explode(',', $name);
        $options = array_map('trim', $options);
        return array_filter($options);
    }
}
