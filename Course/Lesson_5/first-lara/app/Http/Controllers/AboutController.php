<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function index(){

    }
    //action name: about
    public function about(){

        // return '
        // <h1>About FT SD E 19</h1>
        // <p>we are in group FT SD E 19. total 31 students.</p>
        // ';

        $countries = ['Argentina', 'Belgium', 'Canada', 'Cambodia'];
        $students = ['Titrabot', 'Sreyleap', 'Seavyun'];

        return view('about.us', compact('countries', 'stu'));
        // return view('about.us', ['countries'=>$countries, 'students'=>$students]);
    }
}
