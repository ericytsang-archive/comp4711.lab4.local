<?php

class DBLoader extends Application
{
    function show_database_form()
    {
         $this->load->view('database_form');
    }

    function handle_database_form()
    {
        $this->load->helper('file');

        // Get database credentials, and modify the database configuration file
        $username      = $this->input->post('username');
        $password      = $this->input->post('password');
        $database_name = $this->input->post('database_name');
        $this->_modify_database_file($username,$password,$database_name);

        // Try to create the database
        $this->_forge_database($database_name);

        // Redirect to the home page
        //redirect('/');
    }

    // tries to connect to the database.
    // returns TRUE upon success; FALSE otherwise.
    function _modify_database_file($username,$password,$database_name)
    {
        $this->load->helper('file');
        $this->load->helper('parser');
        $this->load->dbforge();

        // Read in the database configuration template file
        $database_file = read_file('./database_template.tmp');

        // Inject credentials into database configuration template (in memory)
        $database_file = replace_placeholders($database_file
            ,'username',$username);
        $database_file = replace_placeholders($database_file
            ,'password',$password);
        $database_file = replace_placeholders($database_file
            ,'database_name',$database_name);

        // Write to the real database configuration file
        return write_file('./application/config/database.php', $database_file);
    }

    function _forge_database($database_name)
    {
        if($this->dbforge->create_database($database_name))
        {
            echo 'Database create success!';

            $this->db->close();
            $this->load->database($database_name);

            // Create menu table
            $this->dbforge->create_table('menu',TRUE);
            // $columns = array();
            // $columns['code']        = array('type' => 'int(11)');
            // $columns['name']        = array('type' => 'varchar(32)');
            // $columns['description'] = array('type' => 'varchar(256)');
            // $columns['price']       = array('type' => 'decimal(10,2)');
            // $columns['picture']     = array('type' => 'varchar(100)');
            // $columns['category']    = array('type' => 'varchar(1)');
            // $this->dbforge->add_column('menu',$columns);

            // Create orderitems table
            $this->dbforge->create_table('orderitems',TRUE);

            // Create orders table
            $this->dbforge->create_table('orders',TRUE);
        }
        else
        {
            echo 'Database create fail!';
        }
// -- phpMyAdmin SQL Dump
// -- version 4.1.7
// -- http://www.phpmyadmin.net
// --

// SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
// SET time_zone = "+00:00";


// /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
// /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
// /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
// /*!40101 SET NAMES utf8 */;

// --
// -- Database: `comp4711`
// --

// -- --------------------------------------------------------

// --
// -- Table structure for table `menu`
// --

// DROP TABLE IF EXISTS `menu`;
// CREATE TABLE IF NOT EXISTS `menu` (
//   `code` int(11) NOT NULL,
//   `name` varchar(32) NOT NULL,
//   `description` varchar(256) NOT NULL,
//   `price` decimal(10,2) NOT NULL,
//   `picture` varchar(100) NOT NULL,
//   `category` varchar(1) NOT NULL,
//   PRIMARY KEY (`code`)
// ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

// --
// -- Dumping data for table `menu`
// --

// INSERT INTO `menu` (`code`, `name`, `description`, `price`, `picture`, `category`) VALUES
// (1, 'Cheese', 'Leave this raw milk, beefy and sweet cheese out for an hour before serving and pair with pear jam.', '2.95', '1.png', 's'),
// (2, 'Turkey', 'Roasted, succulent, stuffed, lovingly sliced turkey breast', '5.95', '2.png', 'm'),
// (6, 'Donut', 'Disgustingly sweet, topped with artery clogging chocolate and then sprinkled with Pixie dust', '1.25', '6.png', 's'),
// (10, 'Bubbly', '1964 Moet Charmon, made from grapes crushed by elves with clean feet, perfectly chilled.', '14.50', '10.png', 'd'),
// (11, 'Ice Cream', 'Combination of decadent chocolate topped with luscious strawberry, churned by gifted virgins using only cream from the Tajima strain of wagyu cattle', '3.75', '11.png', 's'),
// (8, 'Hot Dog', 'Pork trimmings mixed with powdered preservatives, flavourings, red colouring and drenched in water before being squeezed into plastic tubes. Topped with onions, bacon, chili or cheese - no extra charge.', '6.90', '8.png', 'm'),
// (25, 'Burger', 'Half-pound of beef, topped with bacon and served with your choice of a slice of American cheese, red onion, sliced tomato, and Heart Attack Grill''s own unique special sauce.', '9.99', 'burger.png', 'm'),
// (21, 'Coffee', 'A delicious cup of the nectar of life, saviour of students, morning kick-starter; made with freshly grounds that you don''t want to know where they came from!', '2.95', 'coffee.png', 'd');

// -- --------------------------------------------------------

// --
// -- Table structure for table `orderitems`
// --

// DROP TABLE IF EXISTS `orderitems`;
// CREATE TABLE IF NOT EXISTS `orderitems` (
//   `order` int(11) NOT NULL,
//   `item` int(11) NOT NULL,
//   `quantity` int(11) NOT NULL,
//   PRIMARY KEY (`order`,`item`)
// ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

// -- --------------------------------------------------------

// --
// -- Table structure for table `orders`
// --

// DROP TABLE IF EXISTS `orders`;
// CREATE TABLE IF NOT EXISTS `orders` (
//   `num` int(11) NOT NULL,
//   `date` datetime NOT NULL,
//   `status` varchar(1) NOT NULL,
//   `total` decimal(10,2) NOT NULL,
//   PRIMARY KEY (`num`)
// ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

// /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
// /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
// /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

    }
}
