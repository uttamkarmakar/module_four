<?php
    /**
   * @file
   * Contains \Drupal\custom_api\Controller\CustomApiController.
   */

  namespace Drupal\custom_api\Controller;

  use Drupal\Core\Controller\ControllerBase;
  use Symfony\Component\HttpFoundation\JsonResponse;
  use Drupal\custom_api\ProductDataService;
  use Symfony\Component\DependencyInjection\ContainerInterface;

  /**
   * Provides a controller for the custom API.
   * 
   * @package Drupal\custom_api\Controller
   * 
   * @author Uttam Karmakar<uttam.karmakar@innoraft.com>
   */
  class CustomApiController extends ControllerBase {

    /**
     * The product data service.
     *
     * @var \Drupal\custom_api\ProductDataService
     */
    protected $productDataService;

    /**
     * Constructs a CustomApiController object.
     *
     * @param \Drupal\custom_api\ProductDataService $product_data_service
     *   The product data service used to fetch product information.
     */
    public function __construct(ProductDataService $product_data_service) {
      $this->productDataService = $product_data_service;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
      return new static(
        $container->get('custom_api.product_data')
      );
    }

    /**
     * Returns a JSON response containing product data.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *   The JSON response containing product data.
     */
    public function listNodes() {
      // Fetching product data using the product data service.
      $node_data = $this->productDataService->getProductData();

      // Creating a JSON response with the fetched product data.
      $response = new JsonResponse($node_data);

      $response->setEncodingOptions(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

      return $response;
    }

}
?>
