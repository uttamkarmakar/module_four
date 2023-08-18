<?php
  namespace Drupal\cart_task\Form;
  use Drupal\node\Entity\Node;
  use Drupal\Core\Form\FormBase;
  use Drupal\Core\Ajax\CssCommand;
  use Drupal\Core\Ajax\HtmlCommand;
  use Drupal\Core\Ajax\AjaxResponse;
  use Drupal\Core\Form\FormStateInterface;
  /**
   * Defines a generic form which contains add to cart and buy now buttons.
   */
  class AddToCartButton extends FormBase {
    /**
     * Initializes the \Drupal\Core\Ajax\AjaxResponse instance.
     *
     * @var \Drupal\Core\Ajax\AjaxResponse
     */
    protected $response;
    /**
     * Storing the AjaxResponse instance to the class variable.
     */
    public function __construct() {
      $this->response = new AjaxResponse();
    }
    /**
     * {@inheritdoc}
     */
    public function getFormId() {
      return 'cart_task_form';
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state, $node = NULL) {
      $form['add_to_cart'] = [
        '#type' => 'button',
        '#value' => $this->t('Add to Cart'),
        '#suffix' => '<div id="add-to-cart"></div>',
        '#ajax' => [
          'callback' => '::addToCart',
          'event' => 'click',
          'progress' => [
            'type' => 'throbber',
            'message' => $this->t('Adding Product To Cart'),
          ],
        ],
      ];
      $form['buy_now'] = [
        '#type' => 'submit',
        '#value' => $this->t('Buy Now'),
        '#submit' => ['::buyNow'],
      ];
      return $form;
    }
    /**
     * Displays message using AJAX when user clicks on 'Add To Cart' button.
     *
     * @param array $form
     *   Stores the data in the form fields.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   Stores the object of FormStateInterface.
     */
    public function addToCart(array &$form, FormStateInterface $form_state) {
      $this->response->addCommand(new HtmlCommand('#add-to-cart', $this->t('Product has been added to cart')));
      $this->response->addCommand(new CssCommand('#add-to-cart', ['color' => '#198754']));
      return $this->response;
    }
    /**
     * Redirects to 'Thank You' page user clicks on 'Buy Now' button.
     *
     * @param array $form
     *   Stores the data in the form fields.
     * @param \Drupal\Core\Form\FormStateInterface $form_state
     *   Stores the object of FormStateInterface.
     */
    public function buyNow(array &$form, FormStateInterface $form_state) {
      $node = \Drupal::routeMatch()->getParameter('node');

    // Ensure that the current page displays a node of the product content type.
    if ($node instanceof Node && $node->getType() === 'product_name') {
      // Get the node ID of the current product.
      $product_nid = $node->id();
      // dd($product_nid);  
      // Redirect to the "Thanks" page and pass the product node ID as a parameter.
      $form_state->setRedirect('cart_task.thanks', ['nid' => $product_nid]);
      // $form_state->setRedirect('cart_task.thanks');
    }
  }
    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
    }
  }