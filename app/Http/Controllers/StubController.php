<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class StubController extends Controller
{
    //
    public function login() {
      return Inertia::render('login');
    }

    //
    public function forgot() {
      return Inertia::render('forgot');
    }

    public function auth() {
      return redirect()->action('StubController@dashboard');
    }

    //
    public function dashboard() {
      return Inertia::render('dashboard');
    }

    //
    public function datasets() {
      return Inertia::render('datasets');
    }
    
    //
    public function pages() {
      return Inertia::render('pages');
    }

    // Import process
    public function import() {
      return Inertia::render('datasets/import');
    }

    //
    public function review() {
      return Inertia::render('datasets/review');
    }

    // Graphs
    public function graphs() {
      return Inertia::render('graphs');
    }

    public function createGraph() {
      return Inertia::render('graphs/create');
    }
}
