<?php

  namespace Drupal\custom_api;

  use Drupal\Core\Entity\EntityTypeManagerInterface;

  /**
   * Provides a service for fetching product data.
   */
  class ProductDataService {

    /**
     * The entity type manager.
     *
     * @var \Drupal\Core\Entity\EntityTypeManagerInterface
     */
    protected $entityTypeManager;

    /**
     * Constructs a ProductDataService object.
     *
     * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
     *   The entity type manager service.
     */
    public function __construct(EntityTypeManagerInterface $entity_type_manager) {
      $this->entityTypeManager = $entity_type_manager;
    }

    /**
     * Fetches product data from nodes of type 'product_name'.
     *
     * @return array
     *   An array of product data, each containing title, description, price, and images.
     */
    public function getProductData() {
      // Load nodes of type 'product_name'.
      $nodes = $this->entityTypeManager
        ->getStorage('node')
        ->loadByProperties(['type' => 'product_name']);

      $node_data = [];

      foreach ($nodes as $node) {
        $field_values = [];

        // Load the node as an entity to access field values.
        $loaded_node = $this->entityTypeManager
          ->getStorage('node')
          ->load($node->id());

        // Retrieve image field values.
        $image_items = $loaded_node->get('field_images')->getValue();
        $images = [];

        // Process each image item.
        foreach ($image_items as $image_item) {
          // Load the image file entity.
          $image_entity = $this->entityTypeManager->getStorage('file')->load($image_item['target_id']);
          if ($image_entity) {
            // Build image data.
            $images[] = [
              'url' => $image_entity->createFileUrl(),
              'alt' => $image_item['alt'],
              'title' => $image_item['title'],
            ];
          }
        }

        // Build an array of product data for each node.
        $node_data[] = [
          'title' => $node->getTitle(),
          'description' => $node->get('body')->getValue()[0]['value'],
          'price' => $node->get('field_price')->getValue()[0]['value'],
          'images' => $images,
        ];
      }

      return $node_data;
    }
}

