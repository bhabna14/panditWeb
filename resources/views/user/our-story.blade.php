@extends('user.layouts.front')

@section('styles')
<style>
    .about-section {
        background-color: #f9f9f9;
        padding: 60px 0;
    }
    .about-title {
        font-size: 36px;
        font-weight: bold;
        margin-bottom: 20px;
        position: relative;
        padding-left: 40px;
        display: flex;
        align-items: center;
    }
    .about-title i {
        font-size: 30px;
        color: #f39c12;
        position: absolute;
        left: 0;
    }
    .about-description {
        font-size: 16px;
        line-height: 1.8;
        color: #555;
    }
    .about-section .col-lg-5 {
        background-color: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }
    .search-bg-pooja {
        background-color: #f4f4f4;
        text-align: center;
    }
    .description {
        font-size: 42px;
        font-weight: bold;
        margin-top: 20px;
        color: #fbf5f5;
    }
</style>
@endsection

@section('content')

<section class="pt-40 pb-40 search-bg-pooja">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <h1 class="description">Our Story</h1>
            </div>
        </div>
    </div>
</section>

<section class="about-section">
    <div class="container">
        <div class="row y-gap-30 justify-content-center">

            <div class="col-lg-10">
                <h2 class="about-title"><i class="fas fa-bullseye"></i> Our Story</h2>
                <p class="about-description">
                    At 33 Crores, our journey started with a powerful realization. Just like we want the best for our kids or take good care of our elderly parents, we believe our celestial parents, the ones who created us, deserve the same love and care. This idea grew from a strong belief that giving pure and high-quality puja essentials to the divine is more than a routine – it's a way to show deep gratitude and respect.
                    Fueled by this belief, we set out on a mission to create a special place for gods and goddesses. Here, every product is made with a touch of purity and devotion. The beginning of 33 Crores marked the start of a unique journey, bringing together tradition, spirituality, and deep respect into each carefully crafted puja item.
                    At this very moment, 33 Crores stands as a radiant symbol of unwavering devotion and unblemished purity. We take immense pride in presenting an exquisite array of puja essentials, each one meticulously crafted to perfection for the most sacred rituals. Our products transcend the ordinary; they serve as conduits for spiritual transcendence, thoughtfully designed to elevate and enrich your profound connection with the divine. In every intricate detail and careful choice of materials, we strive to create a sensory experience that heightens the sacredness of your spiritual practices.
                    
                </p>
            </div>

        </div>
    </div>
</section>

@endsection

@section('scripts')
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
@endsection
