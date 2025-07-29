<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

class TicketController extends Controller {

    private $ticketModel;

    public function __construct() {
        Auth::authenticate();
        $this->ticketModel = $this->model('Ticket');
    }

    /**
     * Display a list of all tickets.
     */
    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = 15;
        $tickets = $this->ticketModel->getAll($page, $perPage);
        $totalTickets = $this->ticketModel->getTotalCount();
        $totalPages = ceil($totalTickets / $perPage);

        $data = [
            'title' => 'مدیریت تیکت',
            'tickets' => $tickets,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'per_page' => $perPage,
                'total_items' => $totalTickets
            ]
        ];
        $this->view('tickets/index', $data);
    }

    /**
     * Show a single ticket and its replies.
     */
    public function show() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if (!$id) {
            redirect('tickets');
        }

        $ticket = $this->ticketModel->findById($id);
        if (!$ticket) {
            // Handle not found error
            redirect('tickets');
        }

        $replies = $this->ticketModel->getReplies($id);

        $data = [
            'title' => 'مشاهده تیکت #' . $ticket->id,
            'ticket' => $ticket,
            'replies' => $replies
        ];

        $this->view('tickets/show', $data);
    }

    /**
     * Store a new reply for a ticket.
     */
    public function storeReply() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('tickets');
        }

        $ticket_id = isset($_POST['ticket_id']) ? (int)$_POST['ticket_id'] : 0;
        $message = $_POST['message'] ?? '';

        if (empty($message) || !$ticket_id) {
            // Handle validation error
            redirect('tickets/show?id=' . $ticket_id);
        }

        $data = [
            'ticket_id' => $ticket_id,
            'user_id' => $_SESSION['user_id'], // Currently logged-in user
            'message' => $message
        ];

        if ($this->ticketModel->addReply($data)) {
            // Success
            redirect('tickets/show?id=' . $ticket_id);
        } else {
            // Handle error
            redirect('tickets/show?id=' . $ticket_id);
        }
    }
}
