<?php

  namespace Drupal\cart_task\Controller;

  use Drupal\Core\Controller\ControllerBase;
  use Symfony\Component\DependencyInjection\ContainerInterface;
  use Symfony\Component\HttpFoundation\Request;
  use Drupal\Core\Entity\EntityTypeManagerInterface;
  use Drupal\Core\Session\AccountProxyInterface;

  /**
   * Returns responses for Cart Task routes.
   */
  class CartTaskController extends ControllerBase {

    protected $entityTypeManager;
    protected $currentUser;

    /**
     * Constructs a CartTaskController object.
     *
     * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
     *   The entity type manager.
     * @param \Drupal\Core\Session\AccountProxyInterface $current_user
     *   The current user.
     */
    public function __construct(EntityTypeManagerInterface $entity_type_manager, AccountProxyInterface $current_user) {
      $this->entityTypeManager = $entity_type_manager;
      $this->currentUser = $current_user;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
      return new static(
        $container->get('entity_type.manager'),
        $container->get('current_user')
      );
    }

    /**
     * Builds the response.
     */
    public function build(Request $request) {
      $nid = $request->query->get('nid');

      // Load the product node using the node ID.
      $product_node = $this->entityTypeManager->getStorage('node')->load($nid);

      if ($product_node->getType() === 'product_name') {
        // Retrieve user name and product images.
        $uid = $product_node->getOwnerId();
        $user = $this->entityTypeManager->getStorage('user')->load($uid);
        $user_name = $user->getDisplayName();

        $image_items = $product_node->get('field_images')->getValue();
        $images = [];

        foreach ($image_items as $image_item) {
          $image_entity = $this->entityTypeManager->getStorage('file')->load($image_item['target_id']);
          if ($image_entity) {
            $images[] = [
              'url' => $image_entity->createFileUrl(),
              'alt' => $image_item['alt'],
              'title' => $image_item['title'],
            ];
          }
        }

        // Build the render array with product details, user name, and images.
        $build = [
          '#theme' => 'product_detail_page',
          '#product_title' => $product_node->getTitle(),
          '#product_description' => $product_node->get('body')->value,
          '#product_price' => $product_node->get('field_price')->value,
          '#user_name' => $user_name,
          '#images' => $images,
          '#cache' => [
            'tags' => ['node_list'],
          ],
        ];

        return $build;
      }

      // If the product node is not found or not of the expected type.
      return [
        '#markup' => $this->t('Product not found.'),
      ];
    }
  }
?>
