@extends('user.layouts.front')

{{-- @section('styles') --}}
<style>
 Previous DemoBest jQueryCodelab 
Vertical Tab Style : Demo 110
Section 1
Section 2
Section 3
SECTION 1
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce semper, magna a ultricies volutpat, mi eros viverra massa, vitae consequat nisi justo in tortor. Proin accumsan felis ac felis dapibus, non iaculis mi varius, mi eros viverra massa.

HTML (CSS Framwork: Bootstrap)
1
2
3
4
5
6
7
8
9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
28
29
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <div class="vertical-tab" role="tabpanel">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#Section1" aria-controls="home" role="tab" data-toggle="tab">Section 1</a></li>
                    <li role="presentation"><a href="#Section2" aria-controls="profile" role="tab" data-toggle="tab">Section 2</a></li>
                    <li role="presentation"><a href="#Section3" aria-controls="messages" role="tab" data-toggle="tab">Section 3</a></li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content tabs">
                    <div role="tabpanel" class="tab-pane fade in active" id="Section1">
                        <h3>Section 1</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce semper, magna a ultricies volutpat, mi eros viverra massa, vitae consequat nisi justo in tortor. Proin accumsan felis ac felis dapibus, non iaculis mi varius, mi eros viverra massa.</p>
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="Section2">
                        <h3>Section 2</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce semper, magna a ultricies volutpat, mi eros viverra massa, vitae consequat nisi justo in tortor. Proin accumsan felis ac felis dapibus, non iaculis mi varius, mi eros viverra massa.</p>
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="Section3">
                        <h3>Section 3</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce semper, magna a ultricies volutpat, mi eros viverra massa, vitae consequat nisi justo in tortor. Proin accumsan felis ac felis dapibus, non iaculis mi varius, mi eros viverra massa.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
CSS (Fonts required: Montserrat.)
1
2
3
4
5
6
7
8
9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
28
29
30
31
32
33
34
35
36
37
38
39
40
41
42
43
44
45
46
47
48
49
50
51
52
53
54
55
56
57
58
59
60
61
62
63
64
65
66
67
68
69
70
71
72
73
74
75
76
77
78
79
80
81
82
83
84
85
86
87
88
89
90
a:hover,
a:focus{
    text-decoration: none;
    outline: none;
}
.vertical-tab{
    font-family: 'Montserrat', sans-serif;
    display: table;
}
.vertical-tab .nav-tabs{
    width: 27%;
    min-width: 27%;
    border: none;
    vertical-align: top;
    display: table-cell;
}
.vertical-tab .nav-tabs li{ float: none; }
.vertical-tab .nav-tabs li a{
    color: #444;
    background: transparent;
    font-size: 17px;
    font-weight: 700;
    letter-spacing: 1px;
    text-align: center;
    text-transform: capitalize;
    padding: 10px 10px;
    margin: 0 10px 15px 0;
    border: 1px solid #eee;
    border-radius: 0;
    position: relative;
    z-index: 1;
    transition: all 0.3s ease 0s;
}
.vertical-tab .nav-tabs li a:hover,
.vertical-tab .nav-tabs li.active a{
    color: #fff;
    background: transparent;
    border: 1px solid #eee;
}
.vertical-tab .nav-tabs li.active a:hover,
.vertical-tab .nav-tabs li.active a{ color: #fff; }
.vertical-tab .nav-tabs li a:before{
    content: "";
    background: #eee;
    height: 50%;
    width: 100%;
    position: absolute;
    bottom: 0;
    left: 0;
    z-index: -1;
    transition: all 0.3s ease 0s;
}
.vertical-tab .nav-tabs li.active a:before,
.vertical-tab .nav-tabs li a:hover:before{
    background-color: #9c3140;
    height: 62%;
    bottom: 20%;
}
.vertical-tab .tab-content{
    color: #000;
    background: linear-gradient(#eee 50%, transparent 50%);
    font-size: 14px;
    font-weight: 500;
    letter-spacing: 1px;
    line-height: 23px;
    padding: 20px;
    border: 1px solid #eee;
    display: table-cell;
}
.vertical-tab .tab-content h3{
    color: #9c3140;
    font-size: 20px;
    font-weight: 700;
    text-transform: uppercase;
    margin: 0 0 7px;
}
@media only screen and (max-width: 479px){
    .vertical-tab .nav-tabs{
        width: 100%;
        display: block;
    }
    .vertical-tab .nav-tabs li a{
        padding: 15px 10px 14px;
        margin: 0 0 15px;
    }   
    .vertical-tab .tab-content{
        font-size: 14px;
        display: block; 
    }
}

  </style>
{{-- @endsection --}}

@section('content')

<section data-anim-wrap class="masthead -type-2 js-mouse-move-container">
  <div class="container">
    <div class="row">
        <div class="col-md-6">
            <div class="vertical-tab" role="tabpanel">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#Section1" aria-controls="home" role="tab" data-toggle="tab">Section 1</a></li>
                    <li role="presentation"><a href="#Section2" aria-controls="profile" role="tab" data-toggle="tab">Section 2</a></li>
                    <li role="presentation"><a href="#Section3" aria-controls="messages" role="tab" data-toggle="tab">Section 3</a></li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content tabs">
                    <div role="tabpanel" class="tab-pane fade in active" id="Section1">
                        <h3>Section 1</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce semper, magna a ultricies volutpat, mi eros viverra massa, vitae consequat nisi justo in tortor. Proin accumsan felis ac felis dapibus, non iaculis mi varius, mi eros viverra massa.</p>
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="Section2">
                        <h3>Section 2</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce semper, magna a ultricies volutpat, mi eros viverra massa, vitae consequat nisi justo in tortor. Proin accumsan felis ac felis dapibus, non iaculis mi varius, mi eros viverra massa.</p>
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="Section3">
                        <h3>Section 3</h3>
                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce semper, magna a ultricies volutpat, mi eros viverra massa, vitae consequat nisi justo in tortor. Proin accumsan felis ac felis dapibus, non iaculis mi varius, mi eros viverra massa.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@endsection

@section('scripts')
@endsection