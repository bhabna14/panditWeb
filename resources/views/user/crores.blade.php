@extends('user.layouts.front')

@section('styles')
    <style>
        .content-section {
            background-color: #f9f8f6;
            padding: 60px 0;
            font-family: 'Georgia', serif;
        }
        .content-title {
            font-size: 32px;
            font-weight: bold;
            color: #8B0000;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        .content-title i {
            margin-right: 10px;
            font-size: 36px;
            color: #DAA520;
        }
        .content-description {
            font-size: 18px;
            line-height: 1.8;
            color: #444;
        }
        .content-descriptions {
            font-size: 18px;
            line-height: 1.8;
            color: white;
        }
        .highlight-text {
            color: #DAA520;
            font-weight: bold;
        }
        .icon-box {
            display: flex;
            align-items: center;
            margin-top: 20px;
        }
        .icon-box i {
            font-size: 28px;
            color: #8B0000;
            margin-right: 15px;
        }
        .icon-box span {
            font-size: 18px;
            color: #444;
        }
        .section-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .section-header h2 {
            font-size: 36px;
            color: #8B0000;
            font-weight: bold;
        }
        .divider {
            height: 4px;
            width: 80px;
            background-color: #DAA520;
            margin: 10px auto 20px;
        }
    </style>
@endsection

@section('content')

<section class="pt-40 pb-40 search-bg-pooja">
    <div class="container">
        <div class="row">
            <div class="contents-wrapper text-center">
                <div class="logo" height="6rem" width="30rem">
                    <div class="low-res-container"></div>
                </div>
                <h1 class="content-title">
                    <i class="fas fa-praying-hands"></i> What is 33 Crores?
                </h1>
                <p class="content-descriptions">
                    In the vast expanse of the cosmos, there exists an intricate divine system—a celestial balance maintained by <span class="highlight-text">33 Crores</span> of deities. These deities, led by Brahma, Vishnu, and Maheswar, form the spiritual backbone of the universe, guiding every aspect of existence.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="content-section">
    <div class="container">
        <div class="section-header">
            <h2>The Meaning Behind 33 Crores</h2>
            <div class="divider"></div>
        </div>
        <p class="content-description">
            The number "<span class="highlight-text">33 Crores</span>" comes from ancient scriptures, representing 33 primary deities who oversee different cosmic functions. Over time, this evolved into the belief in 33 crore divine energies, each playing a crucial role in the cosmic cycle.
        </p>
        <div class="icon-box">
            <i class="fas fa-om"></i>
            <span>33 Crores symbolizes infinite divine energies that sustain creation, preservation, and transformation.</span>
        </div>
        <div class="icon-box">
            <i class="fas fa-hand-holding-heart"></i>
            <span>Devotees seek blessings through pure offerings, prayers, and sacred rituals.</span>
        </div>
    </div>
</section>

<section class="content-section">
    <div class="container">
        <div class="section-header">
            <h2>A Brand Rooted in Purity & Devotion</h2>
            <div class="divider"></div>
        </div>
        <p class="content-description">
            At <span class="highlight-text">33 Crores</span>, we bridge devotion and authenticity by offering meticulously curated puja essentials. Every diya lit, every flower offered, and every incense burned carries a deep spiritual meaning. Our products honor the sacred bond between the devotee and the divine.
        </p>
        <div class="icon-box">
            <i class="fas fa-seedling"></i>
            <span>Pure, premium-quality puja essentials for every ritual.</span>
        </div>
        <div class="icon-box">
            <i class="fas fa-scroll"></i>
            <span>Rooted in ancient traditions, aligned with Vedic customs.</span>
        </div>
    </div>
</section>

<section class="content-section">
    <div class="container">
        <div class="section-header">
            <h2>Why Choose 33 Crores?</h2>
            <div class="divider"></div>
        </div>
        <ul class="content-description">
            <li><i class="fas fa-check-circle" style="color: #DAA520;"></i> Uncompromised Purity – Every product retains natural sanctity.</li>
            <li><i class="fas fa-check-circle" style="color: #DAA520;"></i> Authentic Traditions – Aligned with ancient rituals and customs.</li>
            <li><i class="fas fa-check-circle" style="color: #DAA520;"></i> Divine Connection – Deepen your bond with the divine.</li>
            <li><i class="fas fa-check-circle" style="color: #DAA520;"></i> Sustainability & Ethics – Honoring nature and spirituality.</li>
        </ul>
    </div>
</section>

<section class="content-section">
    <div class="container">
        <div class="section-header">
            <h2>More Than a Brand – A Spiritual Movement</h2>
            <div class="divider"></div>
        </div>
        <p class="content-description">
            <span class="highlight-text">33 Crores</span> is more than just products — it's a revival of sacred devotion. Alongside our offerings, we share spiritual knowledge through blogs, podcasts, and community engagements to help devotees connect deeper with divine traditions.
        </p>
        <div class="icon-box">
            <i class="fas fa-podcast"></i>
            <span>Explore our spiritual podcasts and divine wisdom.</span>
        </div>
    </div>
</section>

<section class="content-section text-center">
    <h2 class="section-header">
        <i class="fas fa-praying-hands"></i> Join Us on This Sacred Journey
    </h2>
    <p class="content-description">
        Let’s make every prayer count, every offering sacred, and every connection divine.
    </p>
    <a href="{{ url('/shop') }}" class="btn btn-primary">Explore Our Offerings</a>
</section>

@endsection

@section('scripts')
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
@endsection
