<?php add_action( 'after_switch_theme', 'create_tbl_produto' );
function create_tbl_produto() {

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'produto';

    $sql = "CREATE TABLE $table_name (
        id int(11) NOT NULL PRIMARY KEY,
        nome varchar(200) NOT NULL,
        descricao text NOT NULL,
        preco varchar(200) NOT NULL,
        UNIQUE KEY id (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

add_action( 'after_switch_theme', 'create_tbl_cliente' );
function create_tbl_cliente() {

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'cliente';

    $sql = "CREATE TABLE $table_name (
        id int(11) NOT NULL PRIMARY KEY,
        nome varchar(200) NOT NULL,
        email varchar(200) NOT NULL,
        telefone varchar(200) NOT NULL,
        UNIQUE KEY id (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

add_action( 'after_switch_theme', 'create_tbl_pedidos' );
function create_tbl_pedidos() {

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->prefix . 'pedido';

    $sql = "CREATE TABLE $table_name (
        id int(11) NOT NULL PRIMARY KEY,
        id_produto int(11) NOT NULL,
        id_cliente int(11) NOT NULL,
        UNIQUE KEY id (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

/* Produtos */
add_action( 'init', 'create_post_type_produto' );
function create_post_type_produto() {
    register_post_type( 'produtos',
        array(
            'labels' => array(
                'name' => _x('Produtos', 'post type general name'),
                'singular_name' => _x('Produto', 'post type singular name'),
                'add_new' => _x('Adicionar', 'produto'),
                'add_new_item' => __('Adicionar produto'),
                'edit_item' => __('Editar produto'),
                'new_item' => __('Novo produto'),
                'all_items' => __('listar'),
                'view_item' => __('Ver produto'),
                'search_items' => __('Procurar produto'),
                'not_found' =>  __('Nenhum produto cadastrado'),
                'not_found_in_trash' => __('Nenhum produto encontrado na lixeira'),
                'menu_name' => ( 'Produtos' )
            ),
            'public' => true,
            'supports' => false,
            'capability_type' => 'post',
            'rewrite' => array("slug" => "produtos"),
            'menu_position' => 4,
            'menu_icon'   => 'dashicons-products',
            'register_meta_box_cb' => 'add_produtos'
        )
    );

}

add_action( 'add_meta_boxes', 'add_produtos' );
function add_produtos() {
    add_meta_box('wp_add_produtos', 'Informações do produto', 'wp_add_produtos', 'produtos', 'normal', 'high');
}

function wp_add_produtos() {

    global $wpdb;


    if($_GET['post']) {
        echo '<input type="hidden" name="idProduto" id="idProduto" value="'.$_GET['post'].'" />';

        $tableName = $wpdb->prefix . 'produto';

        $table = new Table( $tableName );
        $row = $table->select( array('id' => $_GET['post']) ) or die(mysql_error());

        $nome = $row[0]->nome;
        $descricao = $row[0]->descricao;
        $preco = $row[0]->preco;

    }

    echo '<input type="hidden" name="customProdutos" id="customProdutos" value="customProdutos" />';

    echo '<p>Nome:</p>';
    echo '<input type="text" name="_nome" value="' . $nome  . '" class="widefat" />';
    echo '<p>Descrição:</p>';
    echo '<textarea name="_descricao" class="widefat" />' . $descricao  . '</textarea>';
    echo '<p>Preço</p>';
    echo '<input type="text" name="_preco" value="' . $preco  . '" class="widefat" />';

}

add_action('save_post', 'salvarProduto');
function salvarProduto($post_id) {

    global $wpdb;

    if($_POST['customProdutos']) {

        if($_POST['idProduto']) {

            $tableName = $wpdb->prefix . 'produto';

            $table = new Table( $tableName );
            $updated = $table->update( array('nome' => $_POST['_nome'], 'descricao' => $_POST['_descricao'], 'preco' => $_POST['_preco']), array('id' => $_POST['idProduto']) );

        } else {

            $tableName = $wpdb->prefix . 'produto';

            $table = new Table( $tableName );
            $table->insert(array ('id' => $post_id, 'nome' => $_POST['_nome'], 'descricao' => $_POST['_descricao'], 'preco' => $_POST['_preco']) );

        }
    }
}

add_action( 'delete_post', 'deletarProduto');
function deletarProduto ($post_id) {
    global $wpdb;

    $tableName = $wpdb->prefix . 'produto';

    $table = new Table( $tableName );
    $row = $table->select( array('id' => $post_id) );

    if($row[0]->id){
        $excluido = $table->delete( array('id' => $post_id) );
    }

}

add_filter('manage_edit-produtos_columns', 'add_new_produtos_columns');
function add_new_produtos_columns($produtos_columns) {
    $new_columns['cb'] = '<input type="checkbox" />';

    $new_columns['id'] = __('id');
    $new_columns['nome'] = _x('Nome', 'column name');
    $new_columns['descricao'] = __('Descrição');
    $new_columns['preco'] = __('Preço');

    return $new_columns;
}

add_action('manage_produtos_posts_custom_column', 'manage_produtos_columns', 10, 2);
function manage_produtos_columns($column_name, $id) {
    global $wpdb;

    $tableName = $wpdb->prefix . 'produto';
    $table = new Table( $tableName );
    $row = $table->select( array('id' => $id) ) or die(mysql_error());

    switch ($column_name) {

        case 'id':
            echo $id;
                break;

        case 'nome':
            echo '<a href="post.php?post='.$id.'&action=edit">'.$row[0]->nome.'</a>';
                break;

        case 'descricao':
            echo $row[0]->descricao;
                break;

        case 'preco':
            echo $row[0]->preco;
                break;

        default:
        break;
    }
}

add_filter( 'manage_edit-produtos_sortable_columns', 'produtos_sortable_columns' );
function produtos_sortable_columns( $columns ) {

    $columns['id'] = 'id';
    $columns['nome'] = 'nome';
    $columns['preco'] = 'preço';

    return $columns;
}



/* Clientes */
add_action( 'init', 'create_post_type_cliente' );
function create_post_type_cliente() {
    register_post_type( 'clientes',
        array(
            'labels' => array(
                'name' => _x('Clientes', 'post type general name'),
                'singular_name' => _x('Cliente', 'post type singular name'),
                'add_new' => _x('Adicionar', 'cliente'),
                'add_new_item' => __('Adicionar cliente'),
                'edit_item' => __('Editar cliente'),
                'new_item' => __('Novo cliente'),
                'all_items' => __('listar'),
                'view_item' => __('Ver cliente'),
                'search_items' => __('Procurar cliente'),
                'not_found' =>  __('Nenhum cliente cadastrado'),
                'not_found_in_trash' => __('Nenhum cliente encontrado na lixeira'),
                'menu_name' => ( 'Clientes' )
            ),
            'public' => true,
            'supports' => false,
            'capability_type' => 'post',
            'rewrite' => array("slug" => "clientes"),
            'menu_position' => 5,
            'menu_icon'   => 'dashicons-groups',
            'register_meta_box_cb' => 'add_clientes'
        )
    );

}

add_action( 'add_meta_boxes', 'add_clientes' );
function add_clientes() {
    add_meta_box('wp_add_clientes', 'Informações do cliente', 'wp_add_clientes', 'clientes', 'normal', 'high');
}

function wp_add_clientes() {

    global $wpdb;


    if($_GET['post']) {
        echo '<input type="hidden" name="idCliente" id="idCliente" value="'.$_GET['post'].'" />';

        $tableName = $wpdb->prefix . 'cliente';

        $table = new Table( $tableName );
        $row = $table->select( array('id' => $_GET['post']) ) or die(mysql_error());

        $nome = $row[0]->nome;
        $email = $row[0]->email;
        $telefone = $row[0]->telefone;

    }

    echo '<input type="hidden" name="customClientes" id="customClientes" value="customClientes" />';

    echo '<p>Nome:</p>';
    echo '<input type="text" name="_nome" value="' . $nome  . '" class="widefat" />';
    echo '<p>E-mail:</p>';
    echo '<input name="_email" class="widefat" value="' . $email  . '" class="widefat" />';
    echo '<p>Telefone</p>';
    echo '<input type="text" name="_telefone" value="' . $telefone  . '" class="widefat" />';

}

add_action('save_post', 'salvarCliente');
function salvarCliente($post_id) {

    global $wpdb;

    if($_POST['customClientes']) {

        if($_POST['idCliente']) {

            $tableName = $wpdb->prefix . 'cliente';

            $table = new Table( $tableName );
            $updated = $table->update( array('nome' => $_POST['_nome'], 'email' => $_POST['_email'], 'telefone' => $_POST['_telefone']), array('id' => $_POST['idCliente']) );

        } else {

            $tableName = $wpdb->prefix . 'cliente';

            $table = new Table( $tableName );
            $table->insert(array ('id' => $post_id, 'nome' => $_POST['_nome'], 'email' => $_POST['_email'], 'telefone' => $_POST['_telefone']) );

        }
    }
}

add_action( 'delete_post', 'deletarCliente');
function deletarCliente ($post_id) {
    global $wpdb;

    $tableName = $wpdb->prefix . 'cliente';

    $table = new Table( $tableName );
    $row = $table->select( array('id' => $post_id) );

    if($row[0]->id){
        $excluido = $table->delete( array('id' => $post_id) );
    }

}

add_filter('manage_edit-clientes_columns', 'add_new_clientes_columns');
function add_new_clientes_columns($clientes_columns) {
    $new_columns['cb'] = '<input type="checkbox" />';

    $new_columns['id'] = __('id');
    $new_columns['nome'] = _x('Nome', 'column name');
    $new_columns['email'] = __('E-mail');
    $new_columns['telefone'] = __('Telefone');

    return $new_columns;
}

add_action('manage_clientes_posts_custom_column', 'manage_clientes_columns', 10, 2);
function manage_clientes_columns($column_name, $id) {
    global $wpdb;

    $tableName = $wpdb->prefix . 'cliente';
    $table = new Table( $tableName );
    $row = $table->select( array('id' => $id) ) or die(mysql_error());

    switch ($column_name) {

        case 'id':
            echo $id;
                break;

        case 'nome':
            echo '<a href="post.php?post='.$id.'&action=edit">'.$row[0]->nome.'</a>';
                break;

        case 'email':
            echo $row[0]->email;
                break;

        case 'telefone':
            echo $row[0]->telefone;
                break;
        default:
        break;
    }
}

add_filter( 'manage_edit-clientes_sortable_columns', 'clientes_sortable_columns' );
function clientes_sortable_columns( $columns ) {

    $columns['id'] = 'id';
    $columns['nome'] = 'nome';
    $columns['email'] = 'email';
    $columns['telefone'] = 'telefone';

    return $columns;
}


/* Pedidos */
add_action( 'init', 'create_post_type_pedido' );
function create_post_type_pedido() {
    register_post_type( 'pedidos',
        array(
            'labels' => array(
                'name' => _x('Pedidos', 'post type general name'),
                'singular_name' => _x('Pedido', 'post type singular name'),
                'add_new' => _x('Adicionar', 'pedido'),
                'add_new_item' => __('Adicionar pedido'),
                'edit_item' => __('Editar pedido'),
                'new_item' => __('Novo pedido'),
                'all_items' => __('listar'),
                'view_item' => __('Ver pedido'),
                'search_items' => __('Procurar pedido'),
                'not_found' =>  __('Nenhum pedido cadastrado'),
                'not_found_in_trash' => __('Nenhum pedido encontrado na lixeira'),
                'menu_name' => ( 'Pedidos' )
            ),
            'public' => true,
            'supports' => false,
            'capability_type' => 'post',
            'rewrite' => array("slug" => "pedidos"),
            'menu_position' => 6,
            'menu_icon'   => 'dashicons-clipboard',
            'register_meta_box_cb' => 'add_pedidos'
        )
    );
}

add_action( 'add_meta_boxes', 'add_pedidos' );
function add_pedidos() {
    add_meta_box('wp_add_pedidos', 'Informações do pedido', 'wp_add_pedidos', 'pedidos', 'normal', 'high');
}

function wp_add_pedidos() {

    global $wpdb;


    if($_GET['post']) {
        echo '<input type="hidden" name="idPedido" id="idPedido" value="'.$_GET['post'].'" />';

        $tableName = $wpdb->prefix . 'pedido';

        $table = new Table( $tableName );
        $row = $table->select( array('id' => $_GET['post']) ) or die(mysql_error());

        $idProduto = $row[0]->id_produto;
        $idCliente = $row[0]->id_cliente;

    }

    $tableProduto = $wpdb->prefix . 'produto';
    $produto = new Table($tableProduto);
    $allRowsProduto = $produto->selectAll('nome');

    $tableCliente = $wpdb->prefix . 'cliente';
    $cliente = new Table($tableCliente);
    $allRowsCliente = $cliente->selectAll('nome');

    echo '<input type="hidden" name="customPedidos" id="customPedidos" value="customPedidos" />';

    echo '<p>Produtos:</p>';
    echo '<select name="produtoPedido" class="widefat" id="produtoPedido">';
        if(!$_GET['post']) echo '<option disabled selected>Selecione o produto</option>';

        foreach ($allRowsProduto as $value) {
            echo '<option value="'.$value->id.'" ';
                if($value->id == $idProduto) echo 'disabled selected';
            echo ' >'.$value->nome.'</option>';
        }
    echo '</select>';

    echo '<p>Clientes:</p>';
    echo '<select name="clientePedido" class="widefat" id="clientePedido">';
        if(!$_GET['post']) echo '<option disabled selected>Selecione o cliente</option>';

        foreach ($allRowsCliente as $value) {
            echo '<option value="'.$value->id.'" ';
                if($value->id == $idCliente) echo 'disabled selected';
            echo ' >'.$value->nome.'</option>';
        }

    echo '</select>';

}

add_action('save_post', 'salvarPedido');
function salvarPedido($post_id) {

    global $wpdb;

    if($_POST['customPedidos']) {

        if($_POST['idPedido']) {

            $tableName = $wpdb->prefix . 'pedido';

            $table = new Table( $tableName );
            $updated = $table->update( array('id_produto' => $_POST['produtoPedido'], 'id_cliente' => $_POST['clientePedido']), array('id' => $_POST['idPedido']) );

        } else {

            $tableName = $wpdb->prefix . 'pedido';

            $table = new Table( $tableName );
            $table->insert(array ('id' => $post_id, 'id_produto' => $_POST['produtoPedido'], 'id_cliente' => $_POST['clientePedido']) );

        }
    }
}

add_action( 'delete_post', 'deletarPedido');
function deletarPedido ($post_id) {
    global $wpdb;

    $tableName = $wpdb->prefix . 'pedido';

    $table = new Table( $tableName );
    $row = $table->select( array('id' => $post_id) );

    if($row[0]->id){
        $excluido = $table->delete( array('id' => $post_id) );
    }
}

add_filter('manage_edit-pedidos_columns', 'add_new_pedidos_columns');
function add_new_pedidos_columns($pedidos_columns) {
    $new_columns['cb'] = '<input type="checkbox" />';

    $new_columns['id'] = __('id');
    $new_columns['produto'] = _x('Produto', 'column name');
    $new_columns['cliente'] = __('Cliente');

    return $new_columns;
}

add_action('manage_pedidos_posts_custom_column', 'manage_pedidos_columns', 10, 2);
function manage_pedidos_columns($column_name, $id) {
    global $wpdb;

    $tableName = $wpdb->prefix . 'pedido';
    $table = new Table( $tableName );
    $row = $table->select( array('id' => $id) ) or die(mysql_error());

    $tableProduto = $wpdb->prefix . 'produto';
    $produto = new Table( $tableProduto );
    $rowProduto = $produto->select( array('id' => $row[0]->id_produto) ) or die(mysql_error());

    $tableCliente = $wpdb->prefix . 'cliente';
    $cliente = new Table( $tableCliente );
    $rowCliente = $cliente->select( array('id' => $row[0]->id_cliente) ) or die(mysql_error());

    switch ($column_name) {

        case 'id':
            echo '<a href="post.php?post='.$id.'&action=edit">'.$id.'</a>';
                break;

        case 'produto':
            echo '<a href="post.php?post='.$id.'&action=edit">'.$rowProduto[0]->nome.'</a>';
                break;

        case 'cliente':
            echo '<a href="post.php?post='.$id.'&action=edit">'.$rowCliente[0]->nome.'</a>';
                break;

        default:
        break;
    }
}

add_filter( 'manage_edit-pedidos_sortable_columns', 'pedidos_sortable_columns' );
function pedidos_sortable_columns( $columns ) {

    $columns['id'] = 'id';
    $columns['nome'] = 'nome';
    $columns['email'] = 'email';
    $columns['telefone'] = 'telefone';

    return $columns;
}




/* Função para CRUD */
class Table extends crudTesteAbril
{
    public function __construct($tableName)
    {
         parent::__construct($tableName);
    }
}

abstract class crudTesteAbril {

    private $tableName = false;

    public function __construct($tableName) {

        $this->tableName = $tableName;
    }

    public function insert(array $data)
    {
        global $wpdb;

        if(empty($data))
        {
            return false;
        }

        $wpdb->insert($this->tableName, $data) or die(mysql_error());

        return $wpdb->insert_id;
    }

    public function select(array $conditionValue, $condition = '=', $returnSingleRow = FALSE)
    {
        global $wpdb;

        try
        {
            $sql = 'SELECT * FROM `'.$this->tableName.'` WHERE ';

            $conditionCounter = 1;
            foreach ($conditionValue as $field => $value)
            {
                if($conditionCounter > 1)
                {
                    $sql .= ' AND ';
                }

                switch(strtolower($condition))
                {
                    case 'in':
                        if(!is_array($value))
                        {
                            throw new Exception("Os valores para consulta no pode ser um array.", 1);
                        }

                        $sql .= $wpdb->prepare('`%s` IN (%s)', $field, implode(',', $value));
                        break;

                    default:
                        $sql .= $wpdb->prepare('`'.$field.'` '.$condition.' %s', $value);
                        break;
                }

                $conditionCounter++;
            }

            $result = $wpdb->get_results($sql);

            if(count($result) == 1 && $returnSingleRow)
            {
                $result = $result[0];
            }

            return $result;
        }
        catch(Exception $ex)
        {
            return false;
        }
    }

    public function selectAll( $orderBy = NULL )
    {
        global $wpdb;

        $sql = 'SELECT * FROM `'.$this->tableName.'`';

        if(!empty($orderBy))
        {
            $sql .= ' ORDER BY ' . $orderBy;
        }

        $all = $wpdb->get_results($sql);

        return $all;
    }


    public function update(array $data, array $conditionValue) {
        global $wpdb;

        if(empty($data))
        {
            return false;
        }

        $updated = $wpdb->update( $this->tableName, $data, $conditionValue);

        return $updated;
    }


    public function delete(array $conditionValue)
    {
        global $wpdb;

        $deleted = $wpdb->delete( $this->tableName, $conditionValue );

        return $deleted;
    }
}


add_action( 'switch_theme', 'drop_tbl_produto' );
function drop_tbl_produto() {

    global $wpdb;

    $tableName = $wpdb->prefix . 'posts';
    $table = new Table( $tableName );

    $excluido = $table->delete( array('post_type' => 'produtos') ) or die(mysql_error());

    $table = $wpdb->prefix . 'produto';
    $wpdb->query("DROP TABLE IF EXISTS $table") or die(mysql_error());
}

add_action( 'switch_theme', 'drop_tbl_cliente' );
function drop_tbl_cliente() {

    global $wpdb;

    $tableName = $wpdb->prefix . 'posts';
    $table = new Table( $tableName );

    $excluido = $table->delete( array('post_type' => 'clientes') ) or die(mysql_error());


    $table = $wpdb->prefix . 'cliente';
    $wpdb->query("DROP TABLE IF EXISTS $table") or die(mysql_error());
}

add_action( 'switch_theme', 'drop_tbl_pedido' );
function drop_tbl_pedido() {

    global $wpdb;

    $tableName = $wpdb->prefix . 'posts';
    $table = new Table( $tableName );

    $excluido = $table->delete( array('post_type' => 'pedidos') ) or die(mysql_error());

    $table = $wpdb->prefix . 'pedido';
    $wpdb->query("DROP TABLE IF EXISTS $table") or die(mysql_error());

}
