<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Repository\OptionRepository;
use App\Utils\ApiError;
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
            return $this->apiResponse(ApiError::new(ApiError::BAD_REQUEST));
        }

        try
        {
            return $this->apiResponse($optionRepository->getOption($option), ['option' => $option]);
        } catch (\InvalidArgumentException)
        {
            return $this->apiResponse(ApiError::new(ApiError::BAD_REQUEST));
        }
    }

    #[Route('/api/options', name: 'api_options', methods: ['GET'])]
    public function options(
        Request $request,
        OptionRepository $optionRepository
    ): Response {
        $badRequest = ApiError::new(ApiError::BAD_REQUEST);

        $options    = $request->query->all()['options'] ?? [];

        if (is_string($options))
        {
            $options = $this->parseOptions($options);
        }

        if ( ! is_array($options) || empty($options))
        {
            return $this->apiResponse($badRequest);
        }

        $values     = $keys = [];

        foreach ($options as $name)
        {
            foreach ($this->parseOptions($name) as $opt)
            {
                if ( ! is_string($opt) || ! preg_match('#^[a-z]#i', $opt))
                {
                    return $this->apiResponse($badRequest);
                }
                $values[$name] = $optionRepository->getOption($opt);
                $keys[]        = $name;
            }
        }

        return $this->apiResponse($values, ['options' => $keys]);
    }

    protected function parseOptions(string $name): array
    {
        $options = explode(',', $name);
        $options = array_map('trim', $options);
        return array_filter($options);
    }
}
