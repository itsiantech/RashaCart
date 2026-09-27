<!-- ====== TRUST BADGES ====== -->
<div class="pv-trust-badges">
    <div class="pv-trust-item">
        <div class="pv-trust-icon">
            <i class="fas fa-shield-alt"></i>
        </div>
        <div class="pv-trust-text">
            <span class="pv-trust-title">100% Original</span>
            <span class="pv-trust-sub">Premium Quality</span>
        </div>
    </div>
    <div class="pv-trust-item">
        <div class="pv-trust-icon">
            <i class="fas fa-truck"></i>
        </div>
        <div class="pv-trust-text">
            <span class="pv-trust-title">Fast Delivery</span>
            <span class="pv-trust-sub">2-5 Days</span>
        </div>
    </div>
    <div class="pv-trust-item">
        <div class="pv-trust-icon">
            <i class="fas fa-headset"></i>
        </div>
        <div class="pv-trust-text">
            <span class="pv-trust-title">Support 24/7</span>
            <span class="pv-trust-sub">We're Here to Help</span>
        </div>
    </div>
</div>

<style>
    /* Trust Badges */
    .pv-trust-badges {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.3rem;
        background: #fff;
        border: 1px solid #eee;
        border-radius: 10px;
        padding: 0.9rem 0.75rem;
    }

    .pv-trust-item {
        display: flex;
        align-items: center;
        gap: 0.3rem;
        flex: 1;
        min-width: 0;
    }

    .pv-trust-icon {
        width: 34px;
        height: 34px;
        min-width: 34px;
        border-radius: 50%;
        background: #fff3ea;
        color: orangered;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
    }

    .pv-trust-text {
        display: flex;
        flex-direction: column;
        line-height: 1.25;
        min-width: 0;
    }

    .pv-trust-title {
        font-size: 0.8rem;
        font-weight: 700;
        color: #333;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .pv-trust-sub {
        font-size: 0.7rem;
        color: #888;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: none;
    }

    @media (max-width: 576px) {
        .pv-trust-badges {
            padding: 0.7rem 0.3rem;
        }

        .pv-trust-icon {
            width: 30px;
            height: 30px;
            min-width: 30px;
            font-size: 0.8rem;
        }

        .pv-trust-title {
            font-size: 0.62rem;
        }

        .pv-trust-sub {
            font-size: 0.5rem;
            display: none;
        }
    }

        @media (max-width: 360px) {
        .pv-trust-badges {
            padding: 0.7rem 0.2rem;
        }

        .pv-trust-icon {
            width: 30px;
            height: 30px;
            min-width: 30px;
            font-size: 0.7rem;
        }

        .pv-trust-title {
            font-size: 0.6rem;
        }

        .pv-trust-sub {
            font-size: 0.5rem;
            display: none;
        }
    }
</style>