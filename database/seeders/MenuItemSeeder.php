<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\MenuItem;

class MenuItemSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Helper to create items
        $add = function (
            string $title,
            ?string $route = null,
            string $type = 'link',        // 'category' or 'link'
            ?MenuItem $parent = null,
            int $order = 0,
            ?string $icon = null
        ): MenuItem {
            return MenuItem::create([
                'title'     => $title,
                'route'     => $route,          // route name or URL path
                'type'      => $type,           // 'category' or 'link'
                'parent_id' => $parent?->id,
                'order'     => $order,
                'icon'      => $icon,
                'status'    => 'active',        // important: model filters by status='active'
            ]);
        };

        // Visual header (optional)
        $catMain = $add('Main', null, 'category', null, 1);

        // -------------------------
        // Group: VENDOR DETAILS
        // -------------------------
        $grpVendor = $add('VENDOR DETAILS', null, 'category', null, 30);
        $add('Add Vendor Flower Price', 'admin.monthWiseFlowerPrice', 'link', $grpVendor, 4);
        $add('Manage Vendor Flower Price', 'admin.manageFlowerPrice', 'link', $grpVendor, 5);

        // -------------------------
        // Group: OFFER DETAILS
        // -------------------------
        $grpOffer = $add('OFFER DETAILS', null, 'category', null, 40);
        $add('Add Refer Offer', 'admin.offerDetails', 'link', $grpOffer, 1);
        $add('Manage Refer Offer', 'admin.manageOfferDetails', 'link', $grpOffer, 2);
        $add('Add Offer Claim', 'refer.offerClaim', 'link', $grpOffer, 3);
        $add('Manage Offer Claim', 'refer.manageOfferClaim', 'link', $grpOffer, 4);

        // -------------------------
        // Group: MARKETING DETAILS
        // -------------------------
        $grpMkt = $add('MARKETING DETAILS', null, 'category', null, 50);
        $add('Visit Place', 'admin.getVisitPlace', 'link', $grpMkt, 1);
        $add('Manage Visit Place', 'admin.visitPlace', 'link', $grpMkt, 2);
        $add('Follow Up', 'admin.followUpSubscriptions', 'link', $grpMkt, 3);
        $add('Add Flower Promotion', 'admin.promotionList', 'link', $grpMkt, 4);
        $add('Manage Flower Promotion', 'admin.manageFlowerPromotion', 'link', $grpMkt, 5);

        // -------------------------
        // Group: DELIVERY DETAILS
        // -------------------------
        $grpDelivery = $add('DELIVERY DETAILS', null, 'category', null, 60);
        $add('Delivery History', '/admin/manage-delivery-history', 'link', $grpDelivery, 1);
        $add('Add Rider', 'admin.addRiderDetails', 'link', $grpDelivery, 2);
        $add('Manage Rider', 'admin.manageRiderDetails', 'link', $grpDelivery, 3);
        $add('Add Apartment Assign', 'admin.addOrderAssign', 'link', $grpDelivery, 4);
        $add('Manage Apartment Assign', 'admin.manageOrderAssign', 'link', $grpDelivery, 5);
        $add('Manage Locality', 'admin.managelocality', 'link', $grpDelivery, 6);

        // -------------------------
        // Group: FINANCE DETAILS
        // -------------------------
        $grpFinance = $add('FINANCE DETAILS', null, 'category', null, 70);
        $add('Add Office Transaction', 'admin.officeTransactionDetails', 'link', $grpFinance, 1);
        $add('Manage Office Transaction', 'manageOfficePayments', 'link', $grpFinance, 2);
        $add('Add Fund Received', 'admin.officeFundReceived', 'link', $grpFinance, 3);
        $add('Manage Fund Received', 'manageOfficeFund', 'link', $grpFinance, 4);

        // -------------------------
        // Group: FINANCE REPORT
        // -------------------------
        $grpReport = $add('FINANCE REPORT', null, 'category', null, 80);
        $add('Subscription Reports', 'subscription.report', 'link', $grpReport, 1);
        $add('Customize Flower Reports', 'report.customize', 'link', $grpReport, 2);
        $add('Pick-up Flower Reports', 'report.flower.pickup', 'link', $grpReport, 3);
        $add('Flower Estimate', '/admin/reports/flower-estimates', 'link', $grpReport, 4);
        $add('Flower Compare', '/admin/reports/flower-compare', 'link', $grpReport, 5);
        $add('Subscription Package Estimates', '/admin/reports/subscription-package-estimates', 'link', $grpReport, 6);

        // -------------------------
        // Single: Festival Calendar
        // -------------------------
        $add('Festival Calendar', 'admin.getFestivalCalendar', 'link', null, 90);
    }
}
