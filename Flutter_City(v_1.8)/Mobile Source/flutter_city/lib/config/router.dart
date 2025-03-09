import 'package:flutter/material.dart';
import 'package:fluttercity/constant/route_paths.dart';
import 'package:fluttercity/ui/app_info/app_info_view.dart';
import 'package:fluttercity/ui/app_loading/app_loading_view.dart';
import 'package:fluttercity/ui/blog/detail/blog_view.dart';
import 'package:fluttercity/ui/blog/list/blog_list_container.dart';
import 'package:fluttercity/ui/category/filter_list/category_filter_list_view.dart';
import 'package:fluttercity/ui/category/list/category_sorting_list_view.dart';
import 'package:fluttercity/ui/city_info/city_info_container_view.dart';
import 'package:fluttercity/ui/collection/header_list/collection_header_list_container.dart';
import 'package:fluttercity/ui/comment/detail/comment_detail_list_view.dart';
import 'package:fluttercity/ui/comment/list/comment_list_view.dart';
import 'package:fluttercity/ui/contact/contact_us_container_view.dart';
import 'package:fluttercity/ui/dashboard/core/dashboard_view.dart';
import 'package:fluttercity/ui/force_update/force_update_view.dart';
import 'package:fluttercity/ui/gallery/detail/gallery_view.dart';
import 'package:fluttercity/ui/gallery/grid/gallery_grid_view.dart';
import 'package:fluttercity/ui/gallery/list/gallery_list_view.dart';
import 'package:fluttercity/ui/history/list/history_list_container.dart';
import 'package:fluttercity/ui/introslider/intro_slider_view.dart';
import 'package:fluttercity/ui/items/collection_item/item_list_by_collection_id_view.dart';
import 'package:fluttercity/ui/items/detail/description_detail/description_detail_view.dart';
import 'package:fluttercity/ui/items/detail/item_detail_view.dart';
import 'package:fluttercity/ui/items/entry/item_entry_container.dart';
import 'package:fluttercity/ui/items/entry/item_image_upload_view.dart';
import 'package:fluttercity/ui/items/favourite/favourite_product_list_container.dart';
import 'package:fluttercity/ui/items/item/user_item_list_view.dart';
import 'package:fluttercity/ui/items/item_upload/item_upload_container_view.dart';
import 'package:fluttercity/ui/items/list_with_filter/filter/category/filter_list_view.dart';
import 'package:fluttercity/ui/items/list_with_filter/filter/filter/item_search_view.dart';
import 'package:fluttercity/ui/items/list_with_filter/filter/sort/item_sorting_view.dart';
import 'package:fluttercity/ui/items/list_with_filter/item_list_with_filter_container.dart';
import 'package:fluttercity/ui/items/promote/CreditCardView.dart';
import 'package:fluttercity/ui/items/promote/InAppPurchaseView.dart';
import 'package:fluttercity/ui/items/promote/ItemPromoteView.dart';
import 'package:fluttercity/ui/items/promote/choose_payment_view.dart';
import 'package:fluttercity/ui/items/promote/pay_stack_view.dart';
import 'package:fluttercity/ui/items/promote/paystack_request_view.dart';
import 'package:fluttercity/ui/language/list/language_list_view.dart';
import 'package:fluttercity/ui/map/google_map_filter_view.dart';
import 'package:fluttercity/ui/map/google_map_pin_view.dart';
import 'package:fluttercity/ui/map/map_filter_view.dart';
import 'package:fluttercity/ui/map/map_pin_view.dart';
import 'package:fluttercity/ui/noti/detail/noti_view.dart';
import 'package:fluttercity/ui/noti/list/noti_list_view.dart';
import 'package:fluttercity/ui/noti/notification_setting/notification_setting_view.dart';
import 'package:fluttercity/ui/paid_ad/paid_ad_item_list_container.dart';
import 'package:fluttercity/ui/privacy_policy/privacy_policy_container.dart';
import 'package:fluttercity/ui/rating/list/rating_list_view.dart';
import 'package:fluttercity/ui/setting/setting_container_view.dart';
import 'package:fluttercity/ui/setting/setting_privacy_policy_view.dart';
import 'package:fluttercity/ui/specification/add_specification/add_specification_view.dart';
import 'package:fluttercity/ui/specification/specification_list_view.dart';
import 'package:fluttercity/ui/status/status_list_view.dart';
import 'package:fluttercity/ui/subcategory/filter/sub_category_search_list_view.dart';
import 'package:fluttercity/ui/subcategory/list/sub_category_grid_view.dart';
import 'package:fluttercity/ui/user/edit_profile/edit_profile_view.dart';
import 'package:fluttercity/ui/user/forgot_password/forgot_password_container_view.dart';
import 'package:fluttercity/ui/user/login/login_container_view.dart';
import 'package:fluttercity/ui/user/more/more_container_view.dart';
import 'package:fluttercity/ui/user/password_update/change_password_view.dart';
import 'package:fluttercity/ui/user/phone/sign_in/phone_sign_in_container_view.dart';
import 'package:fluttercity/ui/user/phone/verify_phone/verify_phone_container_view.dart';
import 'package:fluttercity/ui/user/register/register_container_view.dart';
import 'package:fluttercity/ui/user/verify/verify_email_container_view.dart';
import 'package:fluttercity/utils/utils.dart';
import 'package:fluttercity/viewobject/blog.dart';
import 'package:fluttercity/viewobject/category.dart';
import 'package:fluttercity/viewobject/city_info.dart';
import 'package:fluttercity/viewobject/comment_header.dart';
import 'package:fluttercity/viewobject/default_photo.dart';
import 'package:fluttercity/viewobject/holder/intent_holder/add_specification_intent_holder.dart';
import 'package:fluttercity/viewobject/holder/intent_holder/collection_intent_holder.dart';
import 'package:fluttercity/viewobject/holder/intent_holder/grallery_list_intent_holder.dart';
import 'package:fluttercity/viewobject/holder/intent_holder/item_detail_intent_holder.dart';
import 'package:fluttercity/viewobject/holder/intent_holder/item_entry_image_intent_holder.dart';
import 'package:fluttercity/viewobject/holder/intent_holder/item_entry_intent_holder.dart';
import 'package:fluttercity/viewobject/holder/intent_holder/item_list_intent_holder.dart';
import 'package:fluttercity/viewobject/holder/intent_holder/map_pin_intent_holder.dart';
import 'package:fluttercity/viewobject/holder/intent_holder/sub_category_intent_holder.dart';
import 'package:fluttercity/viewobject/holder/intent_holder/terms_and_condition_intent_holder.dart';
import 'package:fluttercity/viewobject/holder/intent_holder/user_item_list_intent_holder.dart';
import 'package:fluttercity/viewobject/holder/intent_holder/verify_phone_internt_holder.dart';
import 'package:fluttercity/viewobject/holder/item_parameter_holder.dart';
import 'package:fluttercity/viewobject/holder/paid_history_holder.dart';
import 'package:fluttercity/viewobject/holder/paystack_intent_holder.dart';
import 'package:fluttercity/viewobject/holder/paystack_request_intent_holder%20.dart';
import 'package:fluttercity/viewobject/item.dart';
import 'package:fluttercity/viewobject/noti.dart';
import 'package:fluttercity/viewobject/ps_app_info.dart';
import 'package:fluttercity/viewobject/ps_app_version.dart';

Route<dynamic> generateRoute(RouteSettings settings) {
  switch (settings.name) {
    case '/':
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              AppLoadingView());

    case '${RoutePaths.home}':
      return MaterialPageRoute<dynamic>(builder: (BuildContext context) {
        return DashboardView();
      });

    case '${RoutePaths.force_update}':
      return MaterialPageRoute<dynamic>(builder: (BuildContext context) {
        final Object? args = settings.arguments;
        final PSAppVersion psAppVersion = args as PSAppVersion;
        return ForceUpdateView(psAppVersion: psAppVersion);
      });

    case '${RoutePaths.user_register_container}':
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              RegisterContainerView());
    case '${RoutePaths.login_container}':
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              LoginContainerView());

    case '${RoutePaths.contactUs}':
      return MaterialPageRoute<dynamic>(
          builder: (BuildContext context) => ContactUsContainerView());

    case '${RoutePaths.user_verify_email_container}':
      final Object? args = settings.arguments;
      final String userId = args as String;
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              VerifyEmailContainerView(userId: userId));

    case '${RoutePaths.user_forgot_password_container}':
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              ForgotPasswordContainerView());

    case '${RoutePaths.user_phone_signin_container}':
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              PhoneSignInContainerView());

    case '${RoutePaths.introSlider}':
      return MaterialPageRoute<dynamic>(builder: (BuildContext context) {
        final Object? args = settings.arguments;
        final int settingSlider = args as int;
        return IntroSliderView(settingSlider:settingSlider);
      });

    case '${RoutePaths.categoryList}':
      return MaterialPageRoute<dynamic>(builder: (BuildContext context) {
        return CategorySortingListView();
      });

    case '${RoutePaths.user_phone_verify_container}':
      final Object? args = settings.arguments;

      final VerifyPhoneIntentHolder verifyPhoneIntentParameterHolder =
          args as VerifyPhoneIntentHolder;
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              VerifyPhoneContainerView(
                userName: verifyPhoneIntentParameterHolder.userName!,
                phoneNumber: verifyPhoneIntentParameterHolder.phoneNumber!,
                phoneId: verifyPhoneIntentParameterHolder.phoneId!,
              ));

    case '${RoutePaths.user_update_password}':
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              ChangePasswordView());


    case '${RoutePaths.languageList}':
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              LanguageListView());

    case '${RoutePaths.uploaded}':
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              UploadedItemContainerView());

    case '${RoutePaths.more}':
      return MaterialPageRoute<dynamic>(builder: (BuildContext context) {
        final Object? args = settings.arguments;
        final String userName = args as String;
        return MoreContainerView(userName: userName);
      });

    case '${RoutePaths.appinfo}':
      return MaterialPageRoute<dynamic>(
          builder: (BuildContext context) => AppInfoView());

    // case '${RoutePaths.categoryList}':
    //   final Object? args = settings.arguments;
    //   final String title = args ?? String;
    //   return PageRouteBuilder<dynamic>(
    //       pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
    //           CategoryListViewContainerView(appBarTitle: title));

    case '${RoutePaths.notiList}':
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              const NotiListView());
    case '${RoutePaths.creditCard}':
      final Object? args = settings.arguments;

      final PaidHistoryHolder paidHistoryHolder = args as PaidHistoryHolder;
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              CreditCardView(
                item: paidHistoryHolder.item!,
                amount: paidHistoryHolder.amount!,
                howManyDay: paidHistoryHolder.howManyDay!,
                paymentMethod: paidHistoryHolder.paymentMethod!,
                stripePublishableKey: paidHistoryHolder.stripePublishableKey!,
                startDate: paidHistoryHolder.startDate!,
                startTimeStamp: paidHistoryHolder.startTimeStamp!,
                itemPaidHistoryProvider:
                    paidHistoryHolder.itemPaidHistoryProvider!,
              ));

    case '${RoutePaths.notiSetting}':
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              NotificationSettingView());
    case '${RoutePaths.setting}':
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              SettingContainerView());
    case '${RoutePaths.subCategoryList}':
      final Object? args = settings.arguments;
      final Category category = args as Category;
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              SubCategoryGridView(category: category));

    case '${RoutePaths.noti}':
      final Object? args = settings.arguments;
      final Noti noti = args as Noti;
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              NotiView(noti: noti));

    case '${RoutePaths.filterItemList}':
      final Object? args = settings.arguments;
      final ItemListIntentHolder itemListIntentHolder =
          args as ItemListIntentHolder;
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              ItemListWithFilterContainerView(
                appBarTitle: itemListIntentHolder.appBarTitle,
                // item: itemListIntentHolder.itemParameterHolder,
                itemParameterHolder: itemListIntentHolder.itemParameterHolder,
              ));

    case '${RoutePaths.itemEntry}':
      return MaterialPageRoute<dynamic>(builder: (BuildContext context) {
        final Object? args = settings.arguments;
        final ItemEntryIntentHolder itemEntryIntentHolder =
            args as ItemEntryIntentHolder;
        return ItemEntryContainerView(
          flag: itemEntryIntentHolder.flag,
          item: itemEntryIntentHolder.item,
        );
      });

    case '${RoutePaths.imageUpload}':
      return MaterialPageRoute<dynamic>(builder: (BuildContext context) {
        final Object? args = settings.arguments;
        final ItemEntryImageIntentHolder itemEntryImageIntentHolder =
            args as ItemEntryImageIntentHolder;
        return ItemImageUploadView(
          flag: itemEntryImageIntentHolder.flag!,
          itemId: itemEntryImageIntentHolder.itemId!,
          image: itemEntryImageIntentHolder.image,
          galleryProvider: itemEntryImageIntentHolder.provider!,
        );
      });

    case '${RoutePaths.specificationList}':
      final Object? args = settings.arguments;
      final String itemId = args as String;
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              SpecificationListView(
                itemId: itemId,
              ));

    case '${RoutePaths.addSpecification}':
      final Object? args = settings.arguments;
      final SpecificationIntentHolder specificationIntentHolder =
          args as SpecificationIntentHolder;

      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              AddSpecificationView(
                itemId: specificationIntentHolder.itemId,
                specificationProvider:
                    specificationIntentHolder.specificationProvider,
                name: specificationIntentHolder.name,
                description: specificationIntentHolder.description,
              ));

    case '${RoutePaths.termandcondition}':
      final Object? args = settings.arguments;
      final TermsAndConditionIntentHolder termsAndConditionIntentHolder =
          args as TermsAndConditionIntentHolder;
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              SettingPrivacyPolicyView(
                checkType: termsAndConditionIntentHolder.checkType,
                description: termsAndConditionIntentHolder.description,
              ));

    case '${RoutePaths.privacyPolicy}':
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              PrivacyPolicyContainerView());

    case '${RoutePaths.itemPromote}':
      return MaterialPageRoute<dynamic>(builder: (BuildContext context) {
        final Object? args = settings.arguments;
        final Item item = args as Item;
        return ItemPromoteView(item: item);
      });

    case '${RoutePaths.blogList}':
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              BlogListContainerView());

    case '${RoutePaths.blogDetail}':
      final Object? args = settings.arguments;
      final Blog blog = args as Blog;
      return MaterialPageRoute<Widget>(builder: (BuildContext context) {
        return BlogView(
          blog: blog,
          heroTagImage: blog.id,
        );
      });

    case '${RoutePaths.paidAdItemList}':
      return MaterialPageRoute<dynamic>(
          builder: (BuildContext context) => PaidItemListContainerView());

    case '${RoutePaths.userItemList}':
      return MaterialPageRoute<dynamic>(builder: (BuildContext context) {
        final Object? args = settings.arguments;
        final UserItemListIntentHolder itemEntryIntentHolder =
            args as UserItemListIntentHolder;
        return UserItemListView(
          addedUserId: itemEntryIntentHolder.userId,
          status: itemEntryIntentHolder.status,
          title: itemEntryIntentHolder.title,
        );
      });

    case '${RoutePaths.cityInfoContainerView}':
      final Object? args = settings.arguments;
      final CityInfo cityInfo = args as CityInfo;
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              CityInfoContainerView(cityInfo: cityInfo));

    case '${RoutePaths.historyList}':
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              HistoryListContainerView());

    case '${RoutePaths.descriptionDetail}':
      final Object? args = settings.arguments;
      final Item itemData = args as Item;
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              DescriptionDetailView(itemData: itemData));
    case '${RoutePaths.itemDetail}':
      final Object? args = settings.arguments;
      final ItemDetailIntentHolder holder = args as ItemDetailIntentHolder;
      return MaterialPageRoute<Widget>(builder: (BuildContext context) {
        return ItemDetailView(
          itemId: holder.itemId,
          heroTagImage: holder.heroTagImage,
          heroTagTitle: holder.heroTagTitle,
          heroTagOriginalPrice: holder.heroTagOriginalPrice,
          heroTagUnitPrice: holder.heroTagUnitPrice,
          intentId: holder.id,
          intentQty: holder.qty,
          intentSelectedColorId: holder.selectedColorId,
          intentSelectedColorValue: holder.selectedColorValue,
          intentBasketPrice: holder.basketPrice,
          intentBasketSelectedAttributeList: holder.basketSelectedAttributeList,
        );
      });

    case '${RoutePaths.filterExpantion}':
      final dynamic args = settings.arguments;

      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              FilterListView(selectedData: args));

    case '${RoutePaths.commentList}':
      final Object? args = settings.arguments;
      final Item item = args as Item;
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              CommentListView(item: item));

    case '${RoutePaths.itemSearch}':
      final Object? args = settings.arguments;
      final ItemParameterHolder itemParameterHolder =
          args as ItemParameterHolder;
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              ItemSearchView(itemParameterHolder: itemParameterHolder));

    case '${RoutePaths.mapFilter}':
      return MaterialPageRoute<dynamic>(builder: (BuildContext context) {
        final Object? args = settings.arguments;
        final ItemParameterHolder itemParameterHolder =
            args as ItemParameterHolder;
        return MapFilterView(itemParameterHolder: itemParameterHolder);
      });
      
    case '${RoutePaths.googleMapFilter}':
      return MaterialPageRoute<dynamic>(builder: (BuildContext context) {
        final Object? args = settings.arguments;
        final ItemParameterHolder itemParameterHolder =
            args as ItemParameterHolder;
        return GoogleMapFilterView(
            itemParameterHolder: itemParameterHolder);
      });  

    case '${RoutePaths.mapPin}':
      return MaterialPageRoute<dynamic>(builder: (BuildContext context) {
        final Object? args = settings.arguments;
        final MapPinIntentHolder mapPinIntentHolder =
            args as MapPinIntentHolder;
        return MapPinView(
          flag: mapPinIntentHolder.flag,
          maplat: mapPinIntentHolder.mapLat,
          maplng: mapPinIntentHolder.mapLng,
        );
      });

    case '${RoutePaths.googleMapPin}':
      return MaterialPageRoute<dynamic>(builder: (BuildContext context) {
        final Object? args = settings.arguments;
        final MapPinIntentHolder mapPinIntentHolder =
            args as MapPinIntentHolder;
        return GoogleMapPinView(
          flag: mapPinIntentHolder.flag,
          maplat: mapPinIntentHolder.mapLat,
          maplng: mapPinIntentHolder.mapLng,
        );
      });  

    case '${RoutePaths.itemSort}':
      final Object? args = settings.arguments;
      final ItemParameterHolder itemParameterHolder =
          args as ItemParameterHolder;
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              ItemSortingView(itemParameterHolder: itemParameterHolder));

    case '${RoutePaths.commentDetail}':
      final Object? args = settings.arguments;
      final CommentHeader commentHeader = args as CommentHeader;
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              CommentDetailListView(
                commentHeader: commentHeader,
              ));

    case '${RoutePaths.favouriteItemList}':
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              FavouriteItemListContainerView());

    case '${RoutePaths.collectionProductList}':
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              CollectionHeaderListContainerView());

    case '${RoutePaths.itemListByCollectionId}':
      final Object? args = settings.arguments;
      final CollectionIntentHolder collectionIntentHolder =
          args as CollectionIntentHolder;

      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              ItemListByCollectionIdView(
                itemCollectionHeader:
                    collectionIntentHolder.itemCollectionHeader,
                appBarTitle: collectionIntentHolder.appBarTitle,
              ));

    case '${RoutePaths.ratingList}':
      final Object? args = settings.arguments;
      final String itemDetailId = args as String;
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              RatingListView(itemDetailid: itemDetailId));

    case '${RoutePaths.editProfile}':
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              EditProfileView());

    case '${RoutePaths.galleryGrid}':
      final Object? args = settings.arguments;
      final Item product = args as Item;
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              GalleryGridView(product: product));

    case '${RoutePaths.galleryList}':
      final Object? args = settings.arguments;
      final GalleryListIntentHolder galleryListIntentHolder =
          args as GalleryListIntentHolder;
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              GalleryListView(
                itemId: galleryListIntentHolder.itemId,
                galleryProvider: galleryListIntentHolder.galleryProvider,
              ));

    case '${RoutePaths.payStackRequest}':
      final Object? args = settings.arguments;

      final PayStackRequestInterntHolder payStackRequestInterntHolder =
          args as PayStackRequestInterntHolder;
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              PayStackRequestView(
                item: payStackRequestInterntHolder.item,
                amount: payStackRequestInterntHolder.amount,
                howManyDay: payStackRequestInterntHolder.howManyDay,
                paymentMethod: payStackRequestInterntHolder.paymentMethod,
                stripePublishableKey:
                    payStackRequestInterntHolder.stripePublishableKey,
                startDate: payStackRequestInterntHolder.startDate,
                startTimeStamp: payStackRequestInterntHolder.startTimeStamp,
                itemPaidHistoryProvider:
                    payStackRequestInterntHolder.itemPaidHistoryProvider,
                userProvider: payStackRequestInterntHolder.userProvider,
                userEmail: payStackRequestInterntHolder.userEmail,
                payStackKey: payStackRequestInterntHolder.payStackKey,
              ));

    case '${RoutePaths.payStackPayment}':
      final Object? args = settings.arguments;
      final PayStackInterntHolder payStackInterntHolder =
          args as PayStackInterntHolder;
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              PayStackView(
                item: payStackInterntHolder.item,
                amount: payStackInterntHolder.amount,
                howManyDay: payStackInterntHolder.howManyDay,
                paymentMethod: payStackInterntHolder.paymentMethod,
                stripePublishableKey:
                    payStackInterntHolder.stripePublishableKey,
                startDate: payStackInterntHolder.startDate,
                startTimeStamp: payStackInterntHolder.startTimeStamp,
                itemPaidHistoryProvider:
                    payStackInterntHolder.itemPaidHistoryProvider,
                userProvider: payStackInterntHolder.userProvider,
                payStackKey: payStackInterntHolder.payStackKey,
                userEmail: payStackInterntHolder.userEmail,
              ));

    case '${RoutePaths.galleryDetail}':
      final Object? args = settings.arguments;
      final DefaultPhoto selectedDefaultImage = args as DefaultPhoto;
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              GalleryView(selectedDefaultImage: selectedDefaultImage));

    case '${RoutePaths.choosePayment}':
      return MaterialPageRoute<dynamic>(builder: (BuildContext context) {
        final Map<String, dynamic> args = settings.arguments as Map<String, dynamic>;
        final Item item = args['item'];
        final PSAppInfo appInfo = args['appInfo'];
        Utils.psPrint(appInfo.inAppPurchasedPrdIdAndroid!);
        // final Product product = args ?? Product;
        return ChoosePaymentVIew(item: item, appInfo: appInfo);
      });

    case '${RoutePaths.inAppPurchase}':
      return MaterialPageRoute<dynamic>(builder: (BuildContext context) {
        final Map<String, dynamic> args = settings.arguments as Map<String, dynamic>;
        // final String itemId = args ?? String;
        final String itemId = args['itemId'];
        final PSAppInfo appInfo = args['appInfo'];

        return InAppPurchaseView(itemId, appInfo);
      });

    case '${RoutePaths.searchCategory}':
      final Object? args = settings.arguments;
      final String categoryName = args as String;
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              CategoryFilterListView(categoryName: categoryName));
              
    case '${RoutePaths.searchSubCategory}':
      final Object? args = settings.arguments;
      final SubCategoryIntentHolder subCategoryIntentHolder =
          args as SubCategoryIntentHolder;
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              SubCategorySearchListView(
                categoryId: subCategoryIntentHolder.categoryId,
                subCategoryName: subCategoryIntentHolder.subCategoryName,
              ));
    case '${RoutePaths.statusList}':
      final Object? args = settings.arguments;
      final String statusName = args as String;
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              StatusListView(statusName: statusName));

    default:
      return PageRouteBuilder<dynamic>(
          pageBuilder: (_, Animation<double> a1, Animation<double> a2) =>
              AppLoadingView());
  }
}
