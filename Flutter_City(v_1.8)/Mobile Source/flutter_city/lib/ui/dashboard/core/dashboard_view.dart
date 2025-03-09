// ignore_for_file: unnecessary_null_comparison

import 'dart:io';

import 'package:firebase_auth/firebase_auth.dart' as fb_auth;
import 'package:firebase_dynamic_links/firebase_dynamic_links.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/material.dart';
import 'package:flutter/scheduler.dart' show timeDilation;
import 'package:flutter/services.dart';
import 'package:flutter_facebook_auth/flutter_facebook_auth.dart';
import 'package:fluttercity/config/ps_colors.dart';
import 'package:fluttercity/config/ps_config.dart';
import 'package:fluttercity/constant/ps_constants.dart';
import 'package:fluttercity/constant/ps_dimens.dart';
import 'package:fluttercity/constant/route_paths.dart';
import 'package:fluttercity/provider/app_info/app_info_provider.dart';
import 'package:fluttercity/provider/city_info/city_info_provider.dart';
import 'package:fluttercity/provider/common/notification_provider.dart';
import 'package:fluttercity/provider/delete_task/delete_task_provider.dart';
import 'package:fluttercity/provider/user/user_provider.dart';
import 'package:fluttercity/repository/Common/notification_repository.dart';
import 'package:fluttercity/repository/app_info_repository.dart';
import 'package:fluttercity/repository/city_info_repository.dart';
import 'package:fluttercity/repository/delete_task_repository.dart';
import 'package:fluttercity/repository/item_repository.dart';
import 'package:fluttercity/repository/user_repository.dart';
import 'package:fluttercity/ui/category/list/category_list_view.dart';
import 'package:fluttercity/ui/collection/header_list/collection_header_list_view.dart';
import 'package:fluttercity/ui/common/dialog/confirm_dialog_view.dart';
import 'package:fluttercity/ui/common/dialog/noti_dialog.dart';
import 'package:fluttercity/ui/common/dialog/share_app_dialog.dart';
import 'package:fluttercity/ui/contact/contact_us_view.dart';
import 'package:fluttercity/ui/dashboard/home/home_dashboard_view.dart';
import 'package:fluttercity/ui/history/list/history_list_view.dart';
import 'package:fluttercity/ui/items/favourite/favourite_product_list_view.dart';
import 'package:fluttercity/ui/items/item_upload/item_upload_list_view.dart';
import 'package:fluttercity/ui/items/list_with_filter/item_list_with_filter_view.dart';
import 'package:fluttercity/ui/language/setting/language_setting_view.dart';
import 'package:fluttercity/ui/paid_ad/paid_ad_item_list_view.dart';
import 'package:fluttercity/ui/privacy_policy/privacy_policy_view.dart';
import 'package:fluttercity/ui/search/home_item_search_view.dart';
import 'package:fluttercity/ui/setting/setting_view.dart';
import 'package:fluttercity/ui/user/forgot_password/forgot_password_view.dart';
import 'package:fluttercity/ui/user/login/login_view.dart';
import 'package:fluttercity/ui/user/phone/sign_in/phone_sign_in_view.dart';
import 'package:fluttercity/ui/user/phone/verify_phone/verify_phone_view.dart';
import 'package:fluttercity/ui/user/profile/profile_view.dart';
import 'package:fluttercity/ui/user/register/register_view.dart';
import 'package:fluttercity/ui/user/verify/verify_email_view.dart';
import 'package:fluttercity/utils/utils.dart';
import 'package:fluttercity/viewobject/common/ps_value_holder.dart';
import 'package:fluttercity/viewobject/holder/intent_holder/item_detail_intent_holder.dart';
import 'package:fluttercity/viewobject/holder/intent_holder/item_entry_intent_holder.dart';
import 'package:fluttercity/viewobject/holder/item_parameter_holder.dart';
import 'package:fluttercity/viewobject/item.dart';
import 'package:fluttercity/viewobject/user.dart';
import 'package:fluttericon/font_awesome5_icons.dart';
import 'package:google_sign_in/google_sign_in.dart';
import 'package:provider/provider.dart';
import 'package:provider/single_child_widget.dart';

class DashboardView extends StatefulWidget {
  @override
  _HomeViewState createState() => _HomeViewState();
}

class _HomeViewState extends State<DashboardView>
    with TickerProviderStateMixin, WidgetsBindingObserver {
 late AnimationController animationController;
 late AnimationController animationControllerForFab;

  Animation<double>? animation;

  String appBarTitle = 'Home';
  int _currentIndex = PsConst.REQUEST_CODE__MENU_HOME_FRAGMENT;
  String _userId = '';
  bool isLogout = false;
  bool isFirstTime = true;
  String phoneUserName = '';
  String phoneNumber = '';
  String phoneId = '';

  final GlobalKey<ScaffoldState> scaffoldKey = GlobalKey<ScaffoldState>();
  CityInfoProvider? cityInfoProvider;
  AppInfoProvider? appInfoProvider;
  final FirebaseMessaging _fcm = FirebaseMessaging.instance;
  bool isResumed = false;

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (state == AppLifecycleState.resumed) {
      isResumed = true;
      initDynamicLinks(context);
    }
  }

  @override
  void initState() {
    animationController =
        AnimationController(duration: PsConfig.animation_duration, vsync: this);

    animationControllerForFab = AnimationController(
        duration: const Duration(milliseconds: 300), vsync: this, value: 1);
    initDynamicLinks(context);
    super.initState();
    // Utils.fcmConfigure(context, _fcm);
  }

  @override
  void dispose() {
    animationController.dispose();
    animationControllerForFab.dispose();
    super.dispose();
  }

  Future<void> initDynamicLinks(BuildContext context) async {
    Future<dynamic>.delayed(const Duration(seconds: 3)); //recomme
    String itemId = '';
    if (!isResumed) {
      final PendingDynamicLinkData? data =
          await FirebaseDynamicLinks.instance.getInitialLink();

      if (data != null ) {
        final Uri? deepLink = data.link;
        if (deepLink != null) {
          final String path = deepLink.path;
          final List<String> pathList = path.split('=');
          itemId = pathList[1];
          final ItemDetailIntentHolder holder = ItemDetailIntentHolder(
            itemId: itemId,
            heroTagImage: '-1' + pathList[1] + PsConst.HERO_TAG__IMAGE,
            heroTagTitle: '-1' + pathList[1] + PsConst.HERO_TAG__TITLE,
            heroTagOriginalPrice:
                '-1' + pathList[1] + PsConst.HERO_TAG__ORIGINAL_PRICE,
            heroTagUnitPrice: '-1' + pathList[1] + PsConst.HERO_TAG__UNIT_PRICE,
          );
          Navigator.pushNamed(context, RoutePaths.itemDetail,
              arguments: holder);
        }
      }
    }

FirebaseDynamicLinks.instance.onLink;
    // FirebaseDynamicLinks.instance.onLink(
    //     onSuccess: (PendingDynamicLinkData? dynamicLink) async {
    //   final Uri? deepLink = dynamicLink?.link;
    //   if (deepLink != null) {
    //     final String path = deepLink.path;
    //     final List<String> pathList = path.split('=');
    //     if (itemId == '') {
    //       final ItemDetailIntentHolder holder = ItemDetailIntentHolder(
    //           itemId: pathList[1],
    //           heroTagImage: '-1' + pathList[1] + PsConst.HERO_TAG__IMAGE,
    //           heroTagTitle: '-1' + pathList[1] + PsConst.HERO_TAG__TITLE);
    //       Navigator.pushNamed(context, RoutePaths.itemDetail,
    //           arguments: holder);
    //     }
    //   }
    //   debugPrint('DynamicLinks onLink $deepLink');
    // }, onError: (OnLinkErrorException e) async {
    //   debugPrint('DynamicLinks onError $e');
    // });
  }

  int getBottonNavigationIndex(int param) {
    print('##################################$param');
    int index = 0;
    switch (param) {
      case PsConst.REQUEST_CODE__MENU_HOME_FRAGMENT:
        index = 0;
        break;
      case PsConst.REQUEST_CODE__DASHBOARD_EXPLORER_FRAGMENT:
        index = 1;
        break;
      case PsConst.REQUEST_CODE__DASHBOARD_SELECT_WHICH_USER_FRAGMENT:
        index = 2;
        break;
      case PsConst.REQUEST_CODE__DASHBOARD_REGISTER_FRAGMENT:
        index = 2;
        break;
      case PsConst.REQUEST_CODE__DASHBOARD_FORGOT_PASSWORD_FRAGMENT:
        index = 2;
        break;
      case PsConst.REQUEST_CODE__DASHBOARD_LOGIN_FRAGMENT:
        index = 2;
        break;
      case PsConst.REQUEST_CODE__DASHBOARD_VERIFY_EMAIL_FRAGMENT:
        index = 2;
        break;
      case PsConst.REQUEST_CODE__DASHBOARD_USER_PROFILE_FRAGMENT:
        index = 2;
        break;
      case PsConst.REQUEST_CODE__DASHBOARD_PHONE_SIGNIN_FRAGMENT:
        index = 2;
        break;
      case PsConst.REQUEST_CODE__DASHBOARD_PHONE_VERIFY_FRAGMENT:
        index = 2;
        break;
      case PsConst.REQUEST_CODE__DASHBOARD_CATEGORY_FRAGMENT:
        index = 3;
        break;
      case PsConst.REQUEST_CODE__DASHBOARD_SEARCH_FRAGMENT:
        index = 4;
        break;

      default:
        index = 0;
        break;
    }
    return index;
  }

  dynamic getIndexFromBottonNavigationIndex(int param) {
    int index = PsConst.REQUEST_CODE__MENU_HOME_FRAGMENT;
    String title;
    final PsValueHolder? psValueHolder =
        Provider.of<PsValueHolder?>(context, listen: false);
    switch (param) {
      case 0:
        index = PsConst.REQUEST_CODE__MENU_HOME_FRAGMENT;
        title = Utils.getString('app_name');
        break;
      case 1:
        index = PsConst.REQUEST_CODE__DASHBOARD_EXPLORER_FRAGMENT;
        title = Utils.getString('home__bottom_app_bar_explorer');
        break;
      case 2:
        index = PsConst.REQUEST_CODE__DASHBOARD_SELECT_WHICH_USER_FRAGMENT;
        title = (
                psValueHolder!.userIdToVerify == null ||
                psValueHolder.userIdToVerify == '')
            ? Utils.getString('home__bottom_app_bar_login')
            : Utils.getString('home__bottom_app_bar_verify_email');
        break;
      case 3:
        index = PsConst.REQUEST_CODE__DASHBOARD_CATEGORY_FRAGMENT;
        title = Utils.getString('home__drawer_menu_interest');
        break;
      case 4:
        index = PsConst.REQUEST_CODE__DASHBOARD_SEARCH_FRAGMENT;
        title = Utils.getString('home__bottom_app_bar_search');
        break;
      default:
        index = 0;
        title = Utils.getString('app_name');
        break;
    }
    return <dynamic>[title, index];
  }

  CityInfoRepository? cityInfoRepository;
  UserRepository? userRepository;
  ItemRepository? itemRepository;
  PsValueHolder? valueHolder;
  DeleteTaskRepository? deleteTaskRepository;
  DeleteTaskProvider? deleteTaskProvider;
  NotificationRepository? notificationRepository;
  AppInfoRepository? appInfoRepository;
  UserProvider? userProvider;

  @override
  Widget build(BuildContext context) {
    cityInfoRepository = Provider.of<CityInfoRepository>(context);
    appInfoRepository = Provider.of<AppInfoRepository>(context);
    userRepository = Provider.of<UserRepository>(context);
    valueHolder = Provider.of<PsValueHolder?>(context);
    itemRepository = Provider.of<ItemRepository>(context);
    deleteTaskRepository = Provider.of<DeleteTaskRepository>(context);
    notificationRepository = Provider.of<NotificationRepository>(context);

    timeDilation = 1.0;

    if (isFirstTime) {
      appBarTitle = Utils.getString('app_name');

      Utils.subscribeToTopic(valueHolder!.notiSetting ?? true);
      Utils.fcmConfigure(context, _fcm, valueHolder!.loginUserId ?? '');
      isFirstTime = false;
    }

    Future<void> updateSelectedIndexWithAnimation(
        String title, int index) async {
      await animationController.reverse().then<dynamic>((void data) {
        if (!mounted) {
          return;
        }

        setState(() {
          appBarTitle = title;
          _currentIndex = index;
        });
      });
    }

    Future<void> updateSelectedIndexWithAnimationUserId(
        String title, int index, String? userId) async {
      await animationController.reverse().then<dynamic>((void data) {
        if (!mounted) {
          return;
        }
        if (userId != null) {
          _userId = userId;
        }
        setState(() {
          appBarTitle = title;
          _currentIndex = index;
        });
      });
    }

            Future<void> updateSelectedIndex(int index) async {
      setState(() {
        _currentIndex = index;
      });
    }

    dynamic callLogout(UserProvider provider,
        DeleteTaskProvider deleteTaskProvider, int index) async {
      appBarTitle = Utils.getString( 'app_name');
      updateSelectedIndex(index);
      await provider.replaceLoginUserId('');
      await provider.replaceLoginUserName('');
      await deleteTaskProvider.deleteTask();
      await FacebookAuth.instance.logOut();
      await GoogleSignIn().signOut();
      await fb_auth.FirebaseAuth.instance.signOut();
    }

    Future<bool> _onWillPop() {
      return showDialog<dynamic>(
              context: context,
              builder: (BuildContext context) {
                return ConfirmDialogView(
                    description:
                        Utils.getString('home__quit_dialog_description'),
                    leftButtonText:
                        Utils.getString('app_info__cancel_button_name'),
                    rightButtonText: Utils.getString('dialog__ok'),
                    onAgreeTap: () {
                      SystemNavigator.pop();
                    });
              }) .then((dynamic value) => value as bool);

          //false;
    }

    final Animation<double> animation = Tween<double>(begin: 0.0, end: 1.0)
        .animate(CurvedAnimation(
            parent: animationController,
            curve: const Interval(0.5 * 1, 1.0, curve: Curves.fastOutSlowIn)));

    return WillPopScope(
      onWillPop: _onWillPop,
      child: Scaffold(
          key: scaffoldKey,
          drawer: Drawer(
            child: MultiProvider(
              providers: <SingleChildWidget>[
                ChangeNotifierProvider<UserProvider>(
                    lazy: false,
                    create: (BuildContext context) {
                      return UserProvider(
                          repo: userRepository, psValueHolder: valueHolder);
                    }),
                ChangeNotifierProvider<DeleteTaskProvider>(
                    lazy: false,
                    create: (BuildContext context) {
                      deleteTaskProvider = DeleteTaskProvider(
                          repo: deleteTaskRepository,
                          psValueHolder: valueHolder);
                      return deleteTaskProvider!;
                    }),
              ],
              child: Consumer<UserProvider>(
                builder: (BuildContext context, UserProvider provider,
                    Widget? child) {
                  print(provider.psValueHolder!.loginUserId);
                  return ListView(padding: EdgeInsets.zero, children: <Widget>[
                    _DrawerHeaderWidget(),
                    ListTile(
                      title: Text(Utils.getString('home__drawer_menu_home')),
                    ),
                    _DrawerMenuWidget(
                        icon: Icons.store,
                        title: Utils.getString('home__drawer_menu_home'),
                        index: PsConst.REQUEST_CODE__MENU_HOME_FRAGMENT,
                        onTap: (String title, int index) {
                          Navigator.pop(context);
                          updateSelectedIndexWithAnimation(
                              Utils.getString('app_name'), index);
                        }),
                    _DrawerMenuWidget(
                        icon: Icons.category,
                        title: Utils.getString('home__drawer_menu_interest'),
                        index: PsConst.REQUEST_CODE__MENU_CATEGORY_FRAGMENT,
                        onTap: (String title, int index) {
                          Navigator.pop(context);
                          updateSelectedIndexWithAnimation(title, index);
                        }),
                    _DrawerMenuWidget(
                        icon: Icons.schedule,
                        title: Utils.getString(
                            'home__drawer_menu_latest_destination'),
                        index:
                            PsConst.REQUEST_CODE__MENU_LATEST_PRODUCT_FRAGMENT,
                        onTap: (String title, int index) {
                          Navigator.pop(context);
                          updateSelectedIndexWithAnimation(title, index);
                        }),
                    _DrawerMenuWidget(
                        icon: FontAwesome5.percent,
                        title: Utils.getString('home__drawer_menu_promotion'),
                        index: PsConst
                            .REQUEST_CODE__MENU_DISCOUNT_PRODUCT_FRAGMENT,
                        onTap: (String title, int index) {
                          Navigator.pop(context);
                          updateSelectedIndexWithAnimation(title, index);
                        }),
                    _DrawerMenuWidget(
                        icon: FontAwesome5.gem,
                        title: Utils.getString(
                            'home__menu_drawer_featured_destination'),
                        index: PsConst
                            .REQUEST_CODE__MENU_FEATURED_PRODUCT_FRAGMENT,
                        onTap: (String title, int index) {
                          Navigator.pop(context);
                          updateSelectedIndexWithAnimation(title, index);
                        }),
                    _DrawerMenuWidget(
                        icon: Icons.trending_up,
                        title: Utils.getString(
                            'home__drawer_menu_popular_destination'),
                        index: PsConst
                            .REQUEST_CODE__MENU_TRENDING_PRODUCT_FRAGMENT,
                        onTap: (String title, int index) {
                          Navigator.pop(context);
                          updateSelectedIndexWithAnimation(title, index);
                        }),
                    _DrawerMenuWidget(
                        icon: Icons.folder_open,
                        title: Utils.getString('home__menu_drawer_collection'),
                        index: PsConst.REQUEST_CODE__MENU_COLLECTION_FRAGMENT,
                        onTap: (String title, int index) {
                          Navigator.pop(context);
                          updateSelectedIndexWithAnimation(title, index);
                        }),
                    const Divider(
                      height: PsDimens.space1,
                    ),
                    ListTile(
                      title:
                          Text(Utils.getString('home__menu_drawer_user_info')),
                    ),
                    _DrawerMenuWidget(
                        icon: Icons.person,
                        title: Utils.getString('home__menu_drawer_profile'),
                        index: PsConst
                            .REQUEST_CODE__MENU_SELECT_WHICH_USER_FRAGMENT,
                        onTap: (String title, int index) {
                          Navigator.pop(context);
                          title = (valueHolder == null ||
                                  valueHolder!.userIdToVerify == null ||
                                  valueHolder!.userIdToVerify == '')
                              ? Utils.getString('home__menu_drawer_profile')
                              : Utils.getString(
                                  'home__bottom_app_bar_verify_email');
                          updateSelectedIndexWithAnimation(title, index);
                        }),
                    if (provider != null)
                      if (provider.psValueHolder!.loginUserId != null &&
                          provider.psValueHolder!.loginUserId != '')
                        Visibility(
                          visible: true,
                          child: _DrawerMenuWidget(
                              icon: Icons.cloud,
                              title: Utils.getString(
                                  'home__menu_drawer_uploaded_item'),
                              index: PsConst
                                  .REQUEST_CODE__MENU_UPLOAD_ITEM_FRAGMENT,
                              onTap: (String title, int index) {
                                Navigator.pop(context);
                                updateSelectedIndexWithAnimation(title, index);
                              }),
                        ),
                    if (provider != null)
                      if (provider.psValueHolder!.loginUserId != null &&
                          provider.psValueHolder!.loginUserId != '')
                        Visibility(
                          visible: true,
                          child: _DrawerMenuWidget(
                              icon: Icons.favorite_border,
                              title: Utils.getString(
                                  'home__menu_drawer_favourite'),
                              index:
                                  PsConst.REQUEST_CODE__MENU_FAVOURITE_FRAGMENT,
                              onTap: (String title, int index) {
                                Navigator.pop(context);
                                updateSelectedIndexWithAnimation(title, index);
                              }),
                        ),
                    // if (provider != null)
                    // if (provider.psValueHolder!.loginUserId != null &&
                    //       provider.psValueHolder!.loginUserId != '')
                    //    Visibility(
                    //       visible: true,
                    //       child: _DrawerMenuWidget(
                    //         icon: Icons.swap_horiz,
                    //         title: Utils.getString(
                    //            'home__menu_drawer_paid_ad_transaction'),
                    //         index: PsConst
                    //             .REQUEST_CODE__MENU_PAID_AD_TRANSACTION_FRAGMENT,
                    //         onTap: (String title, int index) {
                    //           Navigator.pop(context);
                    //           updateSelectedIndexWithAnimation(title, index);
                    //         },
                    //       ),
                    // ),
                    if (provider != null)
                      if (provider.psValueHolder!.loginUserId != null &&
                          provider.psValueHolder!.loginUserId != '')
                        Visibility(
                          visible: true,
                          child: _DrawerMenuWidget(
                              icon: Icons.book,
                              title: Utils.getString(
                                  'home__menu_drawer_user_history'),
                              index: PsConst
                                  .REQUEST_CODE__MENU_USER_HISTORY_FRAGMENT,
                              onTap: (String title, int index) {
                                Navigator.pop(context);
                                updateSelectedIndexWithAnimation(title, index);
                              }),
                        ),
                    if (provider != null)
                      if (provider.psValueHolder!.loginUserId != null &&
                          provider.psValueHolder!.loginUserId != '')
                        Visibility(
                          visible: true,
                          child: ListTile(
                            leading: Icon(
                              Icons.power_settings_new,
                              color: PsColors.mainColorWithWhite,
                            ),
                            title: Text(
                              Utils.getString('home__menu_drawer_logout'),
                              style: Theme.of(context).textTheme.bodyMedium,
                            ),
                            onTap: () async {
                              Navigator.pop(context);
                              showDialog<dynamic>(
                                  context: context,
                                  builder: (BuildContext context) {
                                    return ConfirmDialogView(
                                        description: Utils.getString(
                                            'home__logout_dialog_description'),
                                        leftButtonText: Utils.getString(
                                            'home__logout_dialog_cancel_button'),
                                        rightButtonText: Utils.getString(
                                            'home__logout_dialog_ok_button'),
                                        onAgreeTap: () async {
                                          Navigator.of(context).pop();
                                          setState(() {
                                            _currentIndex = PsConst
                                                .REQUEST_CODE__MENU_HOME_FRAGMENT;
                                          });
                                          await provider.replaceLoginUserId('');
                                          await deleteTaskProvider!.deleteTask();
                                          await FacebookAuth.instance.logOut();
                                          await GoogleSignIn().signOut();
                                          await fb_auth.FirebaseAuth.instance
                                              .signOut();
                                        });
                                  });
                            },
                          ),
                        ),
                    const Divider(
                      height: PsDimens.space1,
                    ),
                    ListTile(
                      title: Text(Utils.getString('home__menu_drawer_app')),
                    ),
                    _DrawerMenuWidget(
                        icon: Icons.g_translate,
                        title: Utils.getString('home__menu_drawer_language'),
                        index: PsConst.REQUEST_CODE__MENU_LANGUAGE_FRAGMENT,
                        onTap: (String title, int index) {
                          Navigator.pop(context);
                          updateSelectedIndexWithAnimation('', index);
                        }),
                    _DrawerMenuWidget(
                        icon: Icons.contacts,
                        title: Utils.getString('home__menu_drawer_contact_us'),
                        index: PsConst.REQUEST_CODE__MENU_CONTACT_US_FRAGMENT,
                        onTap: (String title, int index) {
                          Navigator.pop(context);
                          updateSelectedIndexWithAnimation(title, index);
                        }),
                    _DrawerMenuWidget(
                        icon: Icons.settings,
                        title: Utils.getString('home__menu_drawer_setting'),
                        index: PsConst.REQUEST_CODE__MENU_SETTING_FRAGMENT,
                        onTap: (String title, int index) {
                          Navigator.pop(context);
                          updateSelectedIndexWithAnimation(title, index);
                        }),
                    _DrawerMenuWidget(
                        icon: Icons.info_outline,
                        title: Utils.getString('privacy_policy__toolbar_name'),
                        index: PsConst
                            .REQUEST_CODE__MENU_TERMS_AND_CONDITION_FRAGMENT,
                        onTap: (String title, int index) {
                          Navigator.pop(context);
                          updateSelectedIndexWithAnimation(title, index);
                        }),
                    ListTile(
                      leading: Icon(
                        Icons.share,
                        color: PsColors.mainColorWithWhite,
                      ),
                      title: Text(
                        Utils.getString('home__menu_drawer_share_this_app'),
                        style: Theme.of(context).textTheme.bodyMedium,
                      ),
                      onTap: () {
                        Navigator.pop(context);
                        showDialog<dynamic>(
                            context: context,
                            builder: (BuildContext context) {
                              return ShareAppDialog(
                                onPressed: () {
                                  Navigator.pop(context, true);
                                },
                              );
                            });
                      },
                    ),
                    ListTile(
                      leading: Icon(
                        Icons.star_border,
                        color: PsColors.mainColorWithWhite,
                      ),
                      title: Text(
                        Utils.getString('home__menu_drawer_rate_this_app'),
                        style: Theme.of(context).textTheme.bodyMedium,
                      ),
                      onTap: () {
                        Navigator.pop(context);
                        if (Platform.isIOS) {
                          Utils.launchAppStoreURL(
                              iOSAppId: PsConfig.iOSAppStoreId,
                              writeReview: true);
                        } else {
                          Utils.launchURL();
                        }
                      },
                    )
                  ]);
                },
              ),
            ),
          ),
          appBar: AppBar(
            // backgroundColor: (appBarTitle ==
            //             Utils.getString(context, 'home__verify_email') ||
            //         appBarTitle ==
            //             Utils.getString(context, 'home_verify_phone'))
            //     ? PsColors.mainColor
            //     : PsColors.baseColor,
            backgroundColor: Utils.isLightMode(context)
                ? PsColors.mainColor
                : Colors.black12,
            title: Text(
              appBarTitle,
              style: Theme.of(context).textTheme.titleLarge!.copyWith(
                    fontWeight: FontWeight.bold,
                    color: PsColors.white,
                    // color: (appBarTitle ==
                    //             Utils.getString(
                    //                 context, 'home__verify_email') ||
                    //         appBarTitle ==
                    //             Utils.getString(context, 'home_verify_phone'))
                    //     ? PsColors.white
                    //     : PsColors.mainColorWithWhite,
                  ),
            ),
            titleSpacing: 0,
            elevation: 0,
            iconTheme: IconThemeData(color: PsColors.white
                // color: (appBarTitle ==
                //             Utils.getString(context, 'home__verify_email') ||
                //         appBarTitle ==
                //             Utils.getString(context, 'home_verify_phone'))
                //     ? PsColors.white
                //     : PsColors.mainColorWithWhite
                ),
            toolbarTextStyle: TextStyle(color: PsColors.textPrimaryColor),
            systemOverlayStyle: SystemUiOverlayStyle(
              statusBarIconBrightness: Utils.getBrightnessForAppBar(context),
            ),
            actions: <Widget>[
              if (appBarTitle ==
                  Utils.getString('home__menu_drawer_uploaded_item'))
                Visibility(
                  visible: true,
                  child: InkWell(
                    child: Container(
                      margin: const EdgeInsets.only(top: PsDimens.space18),
                      child: Text(
                        'ADD',
                        style: Theme.of(context)
                            .textTheme
                            .titleMedium!
                            .copyWith(color: PsColors.white),
                        textAlign: TextAlign.right,
                      ),
                    ),
                    onTap: () {
                      Navigator.pushNamed(context, RoutePaths.itemEntry,
                          arguments: ItemEntryIntentHolder(
                              flag: PsConst.ADD_NEW_ITEM, item: Item()));
                    },
                  ),
                )
              else
                Container(),
              IconButton(
                icon: Icon(Icons.notifications_none, color: PsColors.white),
                onPressed: () {
                  Navigator.pushNamed(
                    context,
                    RoutePaths.notiList,
                  );
                },
              ),
              if (appBarTitle == Utils.getString('app_name'))
                Visibility(
                  visible: true,
                  child: IconButton(
                    icon: Icon(FontAwesome5.book_open, color: PsColors.white),
                    onPressed: () {
                      Navigator.pushNamed(
                        context,
                        RoutePaths.blogList,
                      );
                    },
                  ),
                )
              else
                Container(),
            ],
          ),
          bottomNavigationBar: _currentIndex ==
                      PsConst.REQUEST_CODE__MENU_HOME_FRAGMENT ||
                  _currentIndex ==
                      PsConst.REQUEST_CODE__DASHBOARD_EXPLORER_FRAGMENT ||
                  _currentIndex ==
                      PsConst
                          .REQUEST_CODE__DASHBOARD_SELECT_WHICH_USER_FRAGMENT ||
                  _currentIndex ==
                      PsConst
                          .REQUEST_CODE__DASHBOARD_USER_PROFILE_FRAGMENT || //go to profile
                  _currentIndex ==
                      PsConst
                          .REQUEST_CODE__DASHBOARD_FORGOT_PASSWORD_FRAGMENT || //go to forgot password
                  _currentIndex ==
                      PsConst
                          .REQUEST_CODE__DASHBOARD_REGISTER_FRAGMENT || //go to register
                  _currentIndex ==
                      PsConst
                          .REQUEST_CODE__DASHBOARD_VERIFY_EMAIL_FRAGMENT || //go to email verify
                  _currentIndex ==
                      PsConst.REQUEST_CODE__DASHBOARD_SEARCH_FRAGMENT ||
                  _currentIndex ==
                      PsConst.REQUEST_CODE__DASHBOARD_CATEGORY_FRAGMENT ||
                  _currentIndex ==
                      PsConst.REQUEST_CODE__DASHBOARD_LOGIN_FRAGMENT ||
                  _currentIndex ==
                      PsConst.REQUEST_CODE__DASHBOARD_PHONE_SIGNIN_FRAGMENT ||
                  _currentIndex ==
                      PsConst.REQUEST_CODE__DASHBOARD_PHONE_VERIFY_FRAGMENT
              ? Visibility(
                  visible: true,
                  child: BottomNavigationBar(
                    type: BottomNavigationBarType.fixed,
                    currentIndex: getBottonNavigationIndex(_currentIndex),
                    showUnselectedLabels: true,
                    backgroundColor: PsColors.backgroundColor,
                    selectedItemColor: PsColors.mainColor,
                    elevation: 10,
                    onTap: (int index) {
                      final dynamic _returnValue =
                          getIndexFromBottonNavigationIndex(index);

                      updateSelectedIndexWithAnimation(
                          _returnValue[0], _returnValue[1]);
                    },
                    items: <BottomNavigationBarItem>[
                      BottomNavigationBarItem(
                        icon: const Icon(
                          Icons.store,
                          size: 20,
                        ),
                        label: Utils.getString('dashboard__home'),
                      ),
                      BottomNavigationBarItem(
                        icon: const Icon(Icons.location_on),
                        label: Utils.getString('home__bottom_app_bar_explorer'),
                      ),
                      BottomNavigationBarItem(
                        icon: const Icon(Icons.person),
                        label: Utils.getString('home__bottom_app_bar_login'),
                      ),
                      BottomNavigationBarItem(
                        icon: const Icon(Icons.dashboard),
                        label: Utils.getString('home__drawer_menu_interest'),
                      ),
                      BottomNavigationBarItem(
                        icon: const Icon(Icons.search),
                        label: Utils.getString('home__bottom_app_bar_search'),
                      ),
                    ],
                  ),
                )
              : null,
          floatingActionButton: _currentIndex ==
                      PsConst.REQUEST_CODE__MENU_HOME_FRAGMENT ||
                  _currentIndex ==
                      PsConst.REQUEST_CODE__DASHBOARD_EXPLORER_FRAGMENT ||
                  _currentIndex ==
                      PsConst
                          .REQUEST_CODE__DASHBOARD_SELECT_WHICH_USER_FRAGMENT ||
                  _currentIndex ==
                      PsConst.REQUEST_CODE__DASHBOARD_USER_PROFILE_FRAGMENT ||
                  _currentIndex ==
                      PsConst
                          .REQUEST_CODE__DASHBOARD_FORGOT_PASSWORD_FRAGMENT ||
                  _currentIndex ==
                      PsConst.REQUEST_CODE__DASHBOARD_REGISTER_FRAGMENT ||
                  _currentIndex ==
                      PsConst.REQUEST_CODE__DASHBOARD_VERIFY_EMAIL_FRAGMENT ||
                  _currentIndex ==
                      PsConst.REQUEST_CODE__DASHBOARD_SEARCH_FRAGMENT ||
                  _currentIndex ==
                      PsConst.REQUEST_CODE__DASHBOARD_CATEGORY_FRAGMENT ||
                  _currentIndex ==
                      PsConst.REQUEST_CODE__DASHBOARD_LOGIN_FRAGMENT ||
                  _currentIndex ==
                      PsConst.REQUEST_CODE__DASHBOARD_PHONE_SIGNIN_FRAGMENT ||
                  _currentIndex ==
                      PsConst.REQUEST_CODE__DASHBOARD_PHONE_VERIFY_FRAGMENT
              ? Container(
                  height: 65.0,
                  width: 65.0,
                  child: FittedBox(
                    child: Container(
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(8.0),
                          boxShadow: <BoxShadow>[
                            BoxShadow(
                                color: PsColors.mainColor.withOpacity(0.3),
                                offset: const Offset(1.1, 1.1),
                                blurRadius: 10.0),
                          ],
                        ),
                        child: Container()),
                  ),
                )
              : null,
          body: ChangeNotifierProvider<NotificationProvider>(
            lazy: false,
            create: (BuildContext context) {
              final NotificationProvider provider = NotificationProvider(
                  repo: notificationRepository, psValueHolder: valueHolder);

              if (provider.psValueHolder!.deviceToken == null ||
                  provider.psValueHolder!.deviceToken == '') {
                final FirebaseMessaging _fcm = FirebaseMessaging.instance;
                Utils.saveDeviceToken(_fcm, provider);
              } else {
                print(
                    'Notification Token is already registered. Notification Setting : true.');
              }

              return provider;
            },
            child: Builder(
              builder: (BuildContext context) {
                // if (_currentIndex ==
                //     PsConst.REQUEST_CODE__DASHBOARD_EXPLORER_FRAGMENT) {
                // 1 Way
                //
                // return MultiProvider(
                //     providers: <SingleChildCloneableWidget>[
                //       ChangeNotifierProvider<CityInfoProvider>(
                //           builder: (BuildContext context) {
                //         provider = CityInfoProvider(repo: repo1);
                //         provider.loadCityInfo();
                //         return provider;
                //       }),
                //       ChangeNotifierProvider<UserInfo
                //     ],
                //     child: CustomScrollView(
                //       scrollDirection: Axis.vertical,
                //       slivers: <Widget>[
                //         _SliverAppbar(
                //           title: 'Shop Info',
                //           scaffoldKey: scaffoldKey,
                //         ),
                //         CityInfoView(
                //             cityInfoProvider: provider,
                //             animationController: animationController,
                //             animation: Tween<double>(begin: 0.0, end: 1.0)
                //                 .animate(CurvedAnimation(
                //                     parent: animationController,
                //                     curve: Interval((1 / 2) * 1, 1.0,
                //                         curve: Curves.fastOutSlowIn))))
                //       ],
                //     ));
                // 2nd Way
                //   return ChangeNotifierProvider<CityInfoProvider>(
                //       lazy: false,
                //       create: (BuildContext context) {
                //         final CityInfoProvider cityInfoProvider =
                //             CityInfoProvider(
                //           repo: cityInfoRepository,
                //           psValueHolder: valueHolder,
                //           // ownerCode: 'DashboardView'
                //         );
                //         cityInfoProvider.loadCityInfo();
                //         return cityInfoProvider;
                //       },
                //       child: CustomScrollView(
                //         scrollDirection: Axis.vertical,
                //         slivers: <Widget>[
                //           // CityInfoView(
                //           //     animationController: animationController,
                //           //     animation: Tween<double>(begin: 0.0, end: 1.0)
                //           //         .animate(CurvedAnimation(
                //           //             parent: animationController,
                //           //             curve: const Interval((1 / 2) * 1, 1.0,
                //           //                 curve: Curves.fastOutSlowIn))))
                //         ],
                //       ));
                // }
                if (_currentIndex ==
                    PsConst
                        .REQUEST_CODE__DASHBOARD_SELECT_WHICH_USER_FRAGMENT) {
                  return ChangeNotifierProvider<UserProvider?>(
                      lazy: false,
                      create: (BuildContext context) {
                       userProvider = UserProvider(
                            repo: userRepository, psValueHolder: valueHolder);
                        //provider.getUserLogin();
                        return userProvider;
                      },
                      child: Consumer<UserProvider>(builder:
                          (BuildContext context, UserProvider provider,
                              Widget? child) {
                        if (provider == null ||
                            provider.psValueHolder!.userIdToVerify == null ||
                            provider.psValueHolder!.userIdToVerify == '') {
                          if (provider == null ||
                              provider.psValueHolder == null ||
                              provider.psValueHolder!.loginUserId == null ||
                              provider.psValueHolder!.loginUserId == '') {
                            return _CallLoginWidget(
                                currentIndex: _currentIndex,
                                animationController: animationController,
                                animation: animation,
                                updateCurrentIndex: (String title, int index) {
                                  if (index != null) {
                                    updateSelectedIndexWithAnimation(
                                        title, index);
                                  }
                                },
                                updateUserCurrentIndex:
                                    (String title, int index, String userId) {
                                  if (index != null) {
                                    updateSelectedIndexWithAnimation(
                                        title, index);
                                  }
                                  if (userId != null) {
                                    _userId = userId;
                                    provider.psValueHolder!.loginUserId = userId;
                                  }
                                });
                          } else {
                            return ProfileView(
                              scaffoldKey: scaffoldKey,
                              animationController: animationController,
                              flag: _currentIndex,
                              userId: provider.psValueHolder!.loginUserId!,
                              callLogoutCallBack: (String userId) {
                              callLogout(provider, deleteTaskProvider!,
                                  PsConst.REQUEST_CODE__MENU_HOME_FRAGMENT);
                            },
                            );
                          }
                        } else {
                          return _CallVerifyEmailWidget(
                              animationController: animationController,
                              animation: animation,
                              currentIndex: _currentIndex,
                              userId: _userId,
                              updateCurrentIndex: (String title, int index) {
                                updateSelectedIndexWithAnimation(title, index);
                              },
                              updateUserCurrentIndex: (String title, int index,
                                  String userId) async {
                                if (userId != null) {
                                  _userId = userId;
                                  provider.psValueHolder!.loginUserId = userId;
                                }
                                setState(() {
                                  appBarTitle = title;
                                  _currentIndex = index;
                                });
                              });
                        }
                      }));
                }
                if (_currentIndex ==
                    PsConst.REQUEST_CODE__DASHBOARD_SEARCH_FRAGMENT) {
                  // 2nd Way
                  //SearchItemProvider searchItemProvider;

                  return CustomScrollView(
                    scrollDirection: Axis.vertical,
                    slivers: <Widget>[
                      HomeItemSearchView(
                          animationController: animationController,
                          animation: animation,
                          itemParameterHolder:
                              ItemParameterHolder().getLatestParameterHolder())
                    ],
                  );
                } else if (_currentIndex ==
                        PsConst.REQUEST_CODE__DASHBOARD_PHONE_SIGNIN_FRAGMENT ||
                    _currentIndex ==
                        PsConst.REQUEST_CODE__MENU_PHONE_SIGNIN_FRAGMENT) {
                  return Stack(children: <Widget>[
                    Container(
                      color: PsColors.mainLightColorWithBlack,
                      width: double.infinity,
                      height: double.maxFinite,
                    ),
                    CustomScrollView(
                        scrollDirection: Axis.vertical,
                        slivers: <Widget>[
                          PhoneSignInView(
                              animationController: animationController,
                              goToLoginSelected: () {
                                animationController
                                    .reverse()
                                    .then<dynamic>((void data) {
                                  if (!mounted) {
                                    return;
                                  }
                                  if (_currentIndex ==
                                      PsConst
                                          .REQUEST_CODE__MENU_PHONE_SIGNIN_FRAGMENT) {
                                    updateSelectedIndexWithAnimation(
                                        Utils.getString('home_login'),
                                        PsConst
                                            .REQUEST_CODE__MENU_LOGIN_FRAGMENT);
                                  }
                                  if (_currentIndex ==
                                      PsConst
                                          .REQUEST_CODE__DASHBOARD_PHONE_SIGNIN_FRAGMENT) {
                                    updateSelectedIndexWithAnimation(
                                        Utils.getString('home_login'),
                                        PsConst
                                            .REQUEST_CODE__DASHBOARD_LOGIN_FRAGMENT);
                                  }
                                });
                              },
                              phoneSignInSelected: (String name, String phoneNo,
                                  String verifyId) {
                                phoneUserName = name;
                                phoneNumber = phoneNo;
                                phoneId = verifyId;
                                if (_currentIndex ==
                                    PsConst
                                        .REQUEST_CODE__MENU_PHONE_SIGNIN_FRAGMENT) {
                                  updateSelectedIndexWithAnimation(
                                      Utils.getString('home_verify_phone'),
                                      PsConst
                                          .REQUEST_CODE__MENU_PHONE_VERIFY_FRAGMENT);
                                }
                                if (_currentIndex ==
                                    PsConst
                                        .REQUEST_CODE__DASHBOARD_PHONE_SIGNIN_FRAGMENT) {
                                  updateSelectedIndexWithAnimation(
                                      Utils.getString('home_verify_phone'),
                                      PsConst
                                          .REQUEST_CODE__DASHBOARD_PHONE_VERIFY_FRAGMENT);
                                }
                              })
                        ])
                  ]);
                } else if (_currentIndex ==
                        PsConst.REQUEST_CODE__DASHBOARD_PHONE_VERIFY_FRAGMENT ||
                    _currentIndex ==
                        PsConst.REQUEST_CODE__MENU_PHONE_VERIFY_FRAGMENT) {
                  return _CallVerifyPhoneWidget(
                      userName: phoneUserName,
                      phoneNumber: phoneNumber,
                      phoneId: phoneId,
                      animationController: animationController,
                      animation: animation,
                      currentIndex: _currentIndex,
                      updateCurrentIndex: (String title, int index) {
                        updateSelectedIndexWithAnimation(title, index);
                      },
                      updateUserCurrentIndex:
                          (String title, int index, String userId) async {
                        if (userId != null) {
                          _userId = userId;
                        }
                        setState(() {
                          appBarTitle = title;
                          _currentIndex = index;
                        });
                      });
                } else if (_currentIndex ==
                        PsConst.REQUEST_CODE__DASHBOARD_USER_PROFILE_FRAGMENT ||
                    _currentIndex ==
                        PsConst.REQUEST_CODE__MENU_USER_PROFILE_FRAGMENT) {
                  return ProfileView(
                    scaffoldKey: scaffoldKey,
                    animationController: animationController,
                    flag: _currentIndex,
                    userId: _userId,
                    callLogoutCallBack: (String userId) {
                              callLogout(userProvider!, deleteTaskProvider!,
                                  PsConst.REQUEST_CODE__MENU_HOME_FRAGMENT);
                            },
                  );
                } else if (_currentIndex ==
                    PsConst.REQUEST_CODE__MENU_CATEGORY_FRAGMENT) {
                  return CategoryListView();
                } else if (_currentIndex ==
                    PsConst.REQUEST_CODE__MENU_LATEST_PRODUCT_FRAGMENT) {
                  return ItemListWithFilterView(
                    key: const Key('1'),
                    animationController: animationController,
                    itemParameterHolder:
                        ItemParameterHolder().getLatestParameterHolder(),
                  );
                } else if (_currentIndex ==
                    PsConst.REQUEST_CODE__MENU_DISCOUNT_PRODUCT_FRAGMENT) {
                  return ItemListWithFilterView(
                    key: const Key('2'),
                    animationController: animationController,
                    itemParameterHolder:
                        ItemParameterHolder().getDiscountParameterHolder(),
                  );
                } else if (_currentIndex ==
                    PsConst.REQUEST_CODE__MENU_TRENDING_PRODUCT_FRAGMENT) {
                  return ItemListWithFilterView(
                    key: const Key('3'),
                    animationController: animationController,
                    itemParameterHolder:
                        ItemParameterHolder().getTrendingParameterHolder(),
                  );
                } else if (_currentIndex ==
                    PsConst.REQUEST_CODE__MENU_FEATURED_PRODUCT_FRAGMENT) {
                  return ItemListWithFilterView(
                    key: const Key('4'),
                    animationController: animationController,
                    itemParameterHolder:
                        ItemParameterHolder().getFeaturedParameterHolder(),
                  );
                } else if (_currentIndex ==
                        PsConst
                            .REQUEST_CODE__DASHBOARD_FORGOT_PASSWORD_FRAGMENT ||
                    _currentIndex ==
                        PsConst.REQUEST_CODE__MENU_FORGOT_PASSWORD_FRAGMENT) {
                  return Stack(children: <Widget>[
                    Container(
                      color: PsColors.mainLightColorWithBlack,
                      width: double.infinity,
                      height: double.maxFinite,
                    ),
                    CustomScrollView(
                        scrollDirection: Axis.vertical,
                        slivers: <Widget>[
                          ForgotPasswordView(
                            animationController: animationController,
                            goToLoginSelected: () {
                              animationController
                                  .reverse()
                                  .then<dynamic>((void data) {
                                if (!mounted) {
                                  return;
                                }
                                if (_currentIndex ==
                                    PsConst
                                        .REQUEST_CODE__MENU_FORGOT_PASSWORD_FRAGMENT) {
                                  updateSelectedIndexWithAnimation(
                                      Utils.getString('home_login'),
                                      PsConst
                                          .REQUEST_CODE__MENU_LOGIN_FRAGMENT);
                                }
                                if (_currentIndex ==
                                    PsConst
                                        .REQUEST_CODE__DASHBOARD_FORGOT_PASSWORD_FRAGMENT) {
                                  updateSelectedIndexWithAnimation(
                                      Utils.getString('home_login'),
                                      PsConst
                                          .REQUEST_CODE__DASHBOARD_LOGIN_FRAGMENT);
                                }
                              });
                            },
                          )
                        ])
                  ]);
                } else if (_currentIndex ==
                        PsConst.REQUEST_CODE__DASHBOARD_REGISTER_FRAGMENT ||
                    _currentIndex ==
                        PsConst.REQUEST_CODE__MENU_REGISTER_FRAGMENT) {
                  return Stack(children: <Widget>[
                    Container(
                      color: PsColors.mainLightColorWithBlack,
                      width: double.infinity,
                      height: double.maxFinite,
                    ),
                    CustomScrollView(scrollDirection: Axis.vertical, slivers: <
                        Widget>[
                      RegisterView(
                          animationController: animationController,
                          onRegisterSelected: (User user) {
                            _userId = user.userId!;
                            // widget.provider.psValueHolder.loginUserId = userId;
                            if (user.status == PsConst.ONE) {
                              updateSelectedIndexWithAnimationUserId(
                                  Utils.getString('home__menu_drawer_profile'),
                                  PsConst
                                      .REQUEST_CODE__DASHBOARD_USER_PROFILE_FRAGMENT,
                                  user.userId);
                            } else {
                              if (_currentIndex ==
                                  PsConst
                                      .REQUEST_CODE__MENU_REGISTER_FRAGMENT) {
                                updateSelectedIndexWithAnimation(
                                    Utils.getString('home__verify_email'),
                                    PsConst
                                        .REQUEST_CODE__MENU_VERIFY_EMAIL_FRAGMENT);
                              } else if (_currentIndex ==
                                  PsConst
                                      .REQUEST_CODE__DASHBOARD_REGISTER_FRAGMENT) {
                                updateSelectedIndexWithAnimation(
                                    Utils.getString('home__verify_email'),
                                    PsConst
                                        .REQUEST_CODE__DASHBOARD_VERIFY_EMAIL_FRAGMENT);
                              } else {
                                updateSelectedIndexWithAnimationUserId(
                                    Utils.getString(
                                        'home__menu_drawer_profile'),
                                    PsConst
                                        .REQUEST_CODE__DASHBOARD_USER_PROFILE_FRAGMENT,
                                    user.userId);
                              }
                            }
                          },
                          goToLoginSelected: () {
                            animationController
                                .reverse()
                                .then<dynamic>((void data) {
                              if (!mounted) {
                                return;
                              }
                              if (_currentIndex ==
                                  PsConst
                                      .REQUEST_CODE__MENU_REGISTER_FRAGMENT) {
                                updateSelectedIndexWithAnimation(
                                    Utils.getString('home_login'),
                                    PsConst.REQUEST_CODE__MENU_LOGIN_FRAGMENT);
                              }
                              if (_currentIndex ==
                                  PsConst
                                      .REQUEST_CODE__DASHBOARD_REGISTER_FRAGMENT) {
                                updateSelectedIndexWithAnimation(
                                    Utils.getString('home_login'),
                                    PsConst
                                        .REQUEST_CODE__DASHBOARD_LOGIN_FRAGMENT);
                              }
                            });
                          })
                    ])
                  ]);
                } else if (_currentIndex ==
                        PsConst.REQUEST_CODE__DASHBOARD_VERIFY_EMAIL_FRAGMENT ||
                    _currentIndex ==
                        PsConst.REQUEST_CODE__MENU_VERIFY_EMAIL_FRAGMENT) {
                  return _CallVerifyEmailWidget(
                      animationController: animationController,
                      animation: animation,
                      currentIndex: _currentIndex,
                      userId: _userId,
                      updateCurrentIndex: (String title, int index) {
                        updateSelectedIndexWithAnimation(title, index);
                      },
                      updateUserCurrentIndex:
                          (String title, int index, String userId) async {
                        if (userId != null) {
                          _userId = userId;
                        }
                        setState(() {
                          appBarTitle = title;
                          _currentIndex = index;
                        });
                      });
                } else if (_currentIndex ==
                        PsConst.REQUEST_CODE__DASHBOARD_LOGIN_FRAGMENT ||
                    _currentIndex ==
                        PsConst.REQUEST_CODE__MENU_LOGIN_FRAGMENT) {
                  return _CallLoginWidget(
                      currentIndex: _currentIndex,
                      animationController: animationController,
                      animation: animation,
                      updateCurrentIndex: (String title, int index) {
                        updateSelectedIndexWithAnimation(title, index);
                      },
                      updateUserCurrentIndex:
                          (String title, int index, String userId) {
                        setState(() {
                          if (index != null) {
                            appBarTitle = title;
                            _currentIndex = index;
                          }
                        });
                        if (userId != null) {
                          _userId = userId;
                        }
                      });
                } else if (_currentIndex ==
                    PsConst.REQUEST_CODE__MENU_SELECT_WHICH_USER_FRAGMENT) {
                  return ChangeNotifierProvider<UserProvider>(
                      lazy: false,
                      create: (BuildContext context) {
                        final UserProvider provider = UserProvider(
                            repo: userRepository, psValueHolder: valueHolder);

                        return provider;
                      },
                      child: Consumer<UserProvider>(builder:
                          (BuildContext context, UserProvider provider,
                              Widget? child) {
                        if (provider == null ||
                            provider.psValueHolder!.userIdToVerify == null ||
                            provider.psValueHolder!.userIdToVerify == '') {
                          if (provider == null ||
                              provider.psValueHolder == null ||
                              provider.psValueHolder!.loginUserId == null ||
                              provider.psValueHolder!.loginUserId == '') {
                            return Stack(
                              children: <Widget>[
                                Container(
                                  color: PsColors.mainLightColorWithBlack,
                                  width: double.infinity,
                                  height: double.maxFinite,
                                ),
                                CustomScrollView(
                                    scrollDirection: Axis.vertical,
                                    slivers: <Widget>[
                                      LoginView(
                                        animationController:
                                            animationController,
                                        animation: animation,
                                        onGoogleSignInSelected:
                                            (String userId) {
                                          setState(() {
                                            _currentIndex = PsConst
                                                .REQUEST_CODE__MENU_USER_PROFILE_FRAGMENT;
                                          });
                                          _userId = userId;
                                          provider.psValueHolder!.loginUserId =
                                              userId;
                                        },
                                        onFbSignInSelected: (String userId) {
                                          setState(() {
                                            _currentIndex = PsConst
                                                .REQUEST_CODE__MENU_USER_PROFILE_FRAGMENT;
                                          });
                                          _userId = userId;
                                          provider.psValueHolder!.loginUserId =
                                              userId;
                                        },
                                        onPhoneSignInSelected: () {
                                          if (_currentIndex ==
                                              PsConst
                                                  .REQUEST_CODE__MENU_PHONE_SIGNIN_FRAGMENT) {
                                            updateSelectedIndexWithAnimation(
                                                Utils.getString(
                                                    'home_phone_signin'),
                                                PsConst
                                                    .REQUEST_CODE__MENU_PHONE_SIGNIN_FRAGMENT);
                                          } else if (_currentIndex ==
                                              PsConst
                                                  .REQUEST_CODE__DASHBOARD_PHONE_SIGNIN_FRAGMENT) {
                                            updateSelectedIndexWithAnimation(
                                                Utils.getString(
                                                    'home_phone_signin'),
                                                PsConst
                                                    .REQUEST_CODE__DASHBOARD_PHONE_SIGNIN_FRAGMENT);
                                          } else if (_currentIndex ==
                                              PsConst
                                                  .REQUEST_CODE__MENU_SELECT_WHICH_USER_FRAGMENT) {
                                            updateSelectedIndexWithAnimation(
                                                Utils.getString(
                                                    'home_phone_signin'),
                                                PsConst
                                                    .REQUEST_CODE__MENU_PHONE_SIGNIN_FRAGMENT);
                                          } else if (_currentIndex ==
                                              PsConst
                                                  .REQUEST_CODE__DASHBOARD_SELECT_WHICH_USER_FRAGMENT) {
                                            updateSelectedIndexWithAnimation(
                                                Utils.getString(
                                                    'home_phone_signin'),
                                                PsConst
                                                    .REQUEST_CODE__DASHBOARD_PHONE_SIGNIN_FRAGMENT);
                                          } else {
                                            updateSelectedIndexWithAnimation(
                                                Utils.getString(
                                                    'home_phone_signin'),
                                                PsConst
                                                    .REQUEST_CODE__DASHBOARD_PHONE_SIGNIN_FRAGMENT);
                                          }
                                        },
                                        onProfileSelected: (String userId) {
                                          setState(() {
                                            _currentIndex = PsConst
                                                .REQUEST_CODE__MENU_USER_PROFILE_FRAGMENT;
                                            _userId = userId;
                                            provider.psValueHolder!.loginUserId =
                                                userId;
                                          });
                                        },
                                        onForgotPasswordSelected: () {
                                          setState(() {
                                            _currentIndex = PsConst
                                                .REQUEST_CODE__MENU_FORGOT_PASSWORD_FRAGMENT;
                                            appBarTitle = Utils.getString(
                                                'home__forgot_password');
                                          });
                                        },
                                        onSignInSelected: () {
                                          updateSelectedIndexWithAnimation(
                                              Utils.getString('home__register'),
                                              PsConst
                                                  .REQUEST_CODE__MENU_REGISTER_FRAGMENT);
                                        },
                                      ),
                                    ])
                              ],
                            );
                          } else {
                            return ProfileView(
                              scaffoldKey: scaffoldKey,
                              animationController: animationController,
                              flag: _currentIndex,
                              userId: provider.psValueHolder!.loginUserId!,
                              callLogoutCallBack: (String userId) {
                              callLogout(provider, deleteTaskProvider!,
                                  PsConst.REQUEST_CODE__MENU_HOME_FRAGMENT);
                            },
                            );
                          }
                        } else {
                          return _CallVerifyEmailWidget(
                              animationController: animationController,
                              animation: animation,
                              currentIndex: _currentIndex,
                              userId: _userId,
                              updateCurrentIndex: (String title, int index) {
                                updateSelectedIndexWithAnimation(title, index);
                              },
                              updateUserCurrentIndex: (String title, int index,
                                  String userId) async {
                                if (userId != null) {
                                  _userId = userId;
                                  provider.psValueHolder!.loginUserId = userId;
                                }
                                setState(() {
                                  appBarTitle = title;
                                  _currentIndex = index;
                                });
                              });
                        }
                      }));
                } else if (_currentIndex ==
                    PsConst.REQUEST_CODE__MENU_UPLOAD_ITEM_FRAGMENT) {
                  return ItemUploadListView(
                      animationController: animationController);
                } else if (_currentIndex ==
                    PsConst.REQUEST_CODE__MENU_FAVOURITE_FRAGMENT) {
                  return FavouriteItemListView(
                      animationController: animationController);
                } else if (_currentIndex ==
                    PsConst.REQUEST_CODE__MENU_PAID_AD_TRANSACTION_FRAGMENT) {
                  final ScrollController scrollController = ScrollController();
                  return PaidAdItemListView(
                    scrollController: scrollController,
                    animationController: animationController,
                  );
                } else if (_currentIndex ==
                    PsConst.REQUEST_CODE__MENU_USER_HISTORY_FRAGMENT) {
                  return HistoryListView(
                      animationController: animationController);
                } else if (_currentIndex ==
                    PsConst.REQUEST_CODE__MENU_COLLECTION_FRAGMENT) {
                  return CollectionHeaderListView(
                      animationController: animationController);
                } else if (_currentIndex ==
                    PsConst.REQUEST_CODE__MENU_LANGUAGE_FRAGMENT) {
                  return LanguageSettingView(
                      animationController: animationController,
                      languageIsChanged: () {});
                } else if (_currentIndex ==
                    PsConst.REQUEST_CODE__MENU_CONTACT_US_FRAGMENT) {
                  return ContactUsView(
                      animationController: animationController);
                } else if (_currentIndex ==
                    PsConst.REQUEST_CODE__MENU_SETTING_FRAGMENT) {
                  return Container(
                    color: PsColors.coreBackgroundColor,
                    height: double.infinity,
                    child: SettingView(
                      animationController: animationController,
                    ),
                  );
                } else if (_currentIndex ==
                    PsConst.REQUEST_CODE__MENU_TERMS_AND_CONDITION_FRAGMENT) {
                  return PrivacyPolicyView(
                    animationController: animationController,
                  );
                } else if (_currentIndex ==
                    PsConst.REQUEST_CODE__DASHBOARD_EXPLORER_FRAGMENT) {
                  return ItemListWithFilterView(
                    key: const Key('1'),
                    animationController: animationController,
                    itemParameterHolder:
                        ItemParameterHolder().getLatestParameterHolder(),
                  );
                } else if (_currentIndex ==
                    PsConst.REQUEST_CODE__DASHBOARD_CATEGORY_FRAGMENT) {
                  return CategoryListView(
                      // animationController: animationController,
                      );
                } else {
                  animationController.forward();
                  return HomeDashboardViewWidget(
                      animationController, context, animationControllerForFab,
                      (String payload) {
                    return showDialog<dynamic>(
                      context: context,
                      builder: (_) {
                        return NotiDialog(message: '$payload');
                      },
                    );
                  });
                }
              },
            ),
          )),
    );
  }
}

class _CallLoginWidget extends StatelessWidget {
  const _CallLoginWidget(
      {required this.animationController,
      required this.animation,
      required this.updateCurrentIndex,
      required this.updateUserCurrentIndex,
      required this.currentIndex});
  final Function updateCurrentIndex;
  final Function updateUserCurrentIndex;
  final AnimationController animationController;
  final Animation<double> animation;
  final int currentIndex;
  @override
  Widget build(BuildContext context) {
    return Stack(
      children: <Widget>[
        Container(
          color: PsColors
              .mainLightColorWithBlack, //ps_wtheme_core_background_color,
          width: double.infinity,
          height: double.maxFinite,
        ),
        CustomScrollView(scrollDirection: Axis.vertical, slivers: <Widget>[
          LoginView(
            animationController: animationController,
            animation: animation,
            onGoogleSignInSelected: (String userId) {
              if (currentIndex == PsConst.REQUEST_CODE__MENU_LOGIN_FRAGMENT) {
                updateUserCurrentIndex(
                    Utils.getString('home__menu_drawer_profile'),
                    PsConst.REQUEST_CODE__MENU_USER_PROFILE_FRAGMENT,
                    userId);
              } else {
                updateUserCurrentIndex(
                    Utils.getString('home__menu_drawer_profile'),
                    PsConst.REQUEST_CODE__DASHBOARD_USER_PROFILE_FRAGMENT,
                    userId);
              }
            },
            onFbSignInSelected: (String userId) {
              if (currentIndex == PsConst.REQUEST_CODE__MENU_LOGIN_FRAGMENT) {
                updateUserCurrentIndex(
                    Utils.getString('home__menu_drawer_profile'),
                    PsConst.REQUEST_CODE__MENU_USER_PROFILE_FRAGMENT,
                    userId);
              } else {
                updateUserCurrentIndex(
                    Utils.getString('home__menu_drawer_profile'),
                    PsConst.REQUEST_CODE__DASHBOARD_USER_PROFILE_FRAGMENT,
                    userId);
              }
            },
            onPhoneSignInSelected: () {
              if (currentIndex == PsConst.REQUEST_CODE__MENU_LOGIN_FRAGMENT) {
                updateCurrentIndex(Utils.getString('home_phone_signin'),
                    PsConst.REQUEST_CODE__MENU_PHONE_SIGNIN_FRAGMENT);
              } else if (currentIndex ==
                  PsConst.REQUEST_CODE__DASHBOARD_LOGIN_FRAGMENT) {
                updateCurrentIndex(Utils.getString('home_phone_signin'),
                    PsConst.REQUEST_CODE__DASHBOARD_PHONE_SIGNIN_FRAGMENT);
              } else if (currentIndex ==
                  PsConst.REQUEST_CODE__MENU_SELECT_WHICH_USER_FRAGMENT) {
                updateCurrentIndex(Utils.getString('home_phone_signin'),
                    PsConst.REQUEST_CODE__MENU_PHONE_SIGNIN_FRAGMENT);
              } else if (currentIndex ==
                  PsConst.REQUEST_CODE__DASHBOARD_SELECT_WHICH_USER_FRAGMENT) {
                updateCurrentIndex(Utils.getString('home_phone_signin'),
                    PsConst.REQUEST_CODE__DASHBOARD_PHONE_SIGNIN_FRAGMENT);
              } else {
                updateCurrentIndex(Utils.getString('home_phone_signin'),
                    PsConst.REQUEST_CODE__DASHBOARD_PHONE_SIGNIN_FRAGMENT);
              }
            },
            onProfileSelected: (String userId) {
              if (currentIndex == PsConst.REQUEST_CODE__MENU_LOGIN_FRAGMENT) {
                updateUserCurrentIndex(
                    Utils.getString('home__menu_drawer_profile'),
                    PsConst.REQUEST_CODE__MENU_USER_PROFILE_FRAGMENT,
                    userId);
              } else {
                updateUserCurrentIndex(
                    Utils.getString('home__menu_drawer_profile'),
                    PsConst.REQUEST_CODE__DASHBOARD_USER_PROFILE_FRAGMENT,
                    userId);
              }
            },
            onForgotPasswordSelected: () {
              if (currentIndex == PsConst.REQUEST_CODE__MENU_LOGIN_FRAGMENT) {
                updateCurrentIndex(Utils.getString('home__forgot_password'),
                    PsConst.REQUEST_CODE__MENU_FORGOT_PASSWORD_FRAGMENT);
              } else {
                updateCurrentIndex(Utils.getString('home__forgot_password'),
                    PsConst.REQUEST_CODE__DASHBOARD_FORGOT_PASSWORD_FRAGMENT);
              }
            },
            onSignInSelected: () {
              if (currentIndex == PsConst.REQUEST_CODE__MENU_LOGIN_FRAGMENT) {
                updateCurrentIndex(Utils.getString('home__register'),
                    PsConst.REQUEST_CODE__MENU_REGISTER_FRAGMENT);
              } else {
                updateCurrentIndex(Utils.getString('home__register'),
                    PsConst.REQUEST_CODE__DASHBOARD_REGISTER_FRAGMENT);
              }
            },
          ),
        ])
      ],
    );
  }
}

class _CallVerifyPhoneWidget extends StatelessWidget {
  const _CallVerifyPhoneWidget(
      {this.userName,
      this.phoneNumber,
      this.phoneId,
      required this.updateCurrentIndex,
      required this.updateUserCurrentIndex,
      required this.animationController,
      required this.animation,
      required this.currentIndex});

  final String? userName;
  final String? phoneNumber;
  final String? phoneId;
  final Function updateCurrentIndex;
  final Function updateUserCurrentIndex;
  final int currentIndex;
  final AnimationController animationController;
  final Animation<double> animation;

  @override
  Widget build(BuildContext context) {
    animationController.forward();
    return SingleChildScrollView(
        scrollDirection: Axis.vertical,
        child: VerifyPhoneView(
          userName: userName!,
          phoneNumber: phoneNumber!,
          phoneId: phoneId!,
          animationController: animationController,
          onProfileSelected: (String userId) {
            if (currentIndex ==
                PsConst.REQUEST_CODE__MENU_PHONE_VERIFY_FRAGMENT) {
              updateUserCurrentIndex(
                  Utils.getString('home__menu_drawer_profile'),
                  PsConst.REQUEST_CODE__MENU_USER_PROFILE_FRAGMENT,
                  userId);
            } else if (currentIndex ==
                PsConst.REQUEST_CODE__DASHBOARD_PHONE_VERIFY_FRAGMENT) {
              updateUserCurrentIndex(
                  Utils.getString('home__menu_drawer_profile'),
                  PsConst.REQUEST_CODE__DASHBOARD_USER_PROFILE_FRAGMENT,
                  userId);
            }
          },
          onSignInSelected: () {
            if (currentIndex ==
                PsConst.REQUEST_CODE__MENU_PHONE_VERIFY_FRAGMENT) {
              updateCurrentIndex(Utils.getString('home_phone_signin'),
                  PsConst.REQUEST_CODE__MENU_PHONE_SIGNIN_FRAGMENT);
            } else if (currentIndex ==
                PsConst.REQUEST_CODE__DASHBOARD_PHONE_VERIFY_FRAGMENT) {
              updateCurrentIndex(Utils.getString('home_phone_signin'),
                  PsConst.REQUEST_CODE__DASHBOARD_PHONE_SIGNIN_FRAGMENT);
            }
          },
        ));
  }
}

class _CallVerifyEmailWidget extends StatelessWidget {
  const _CallVerifyEmailWidget(
      {required this.updateCurrentIndex,
      required this.updateUserCurrentIndex,
      required this.animationController,
      required this.animation,
      required this.currentIndex,
      required this.userId});
  final Function updateCurrentIndex;
  final Function updateUserCurrentIndex;
  final int currentIndex;
  final AnimationController animationController;
  final Animation<double> animation;
  final String userId;

  @override
  Widget build(BuildContext context) {
    animationController.forward();
    return SingleChildScrollView(
        scrollDirection: Axis.vertical,
        child: VerifyEmailView(
          animationController: animationController,
          userId: userId,
          onProfileSelected: (String userId) {
            if (currentIndex ==
                PsConst.REQUEST_CODE__MENU_VERIFY_EMAIL_FRAGMENT) {
              updateUserCurrentIndex(
                  Utils.getString('home__menu_drawer_profile'),
                  PsConst.REQUEST_CODE__MENU_USER_PROFILE_FRAGMENT,
                  userId);
            } else if (currentIndex ==
                PsConst.REQUEST_CODE__DASHBOARD_VERIFY_EMAIL_FRAGMENT) {
              updateUserCurrentIndex(
                  Utils.getString('home__menu_drawer_profile'),
                  PsConst.REQUEST_CODE__DASHBOARD_USER_PROFILE_FRAGMENT,
                  userId);
              // updateCurrentIndex(PsConst.REQUEST_CODE__DASHBOARD_USER_PROFILE_FRAGMENT);
            }
          },
          onSignInSelected: () {
            if (currentIndex ==
                PsConst.REQUEST_CODE__MENU_VERIFY_EMAIL_FRAGMENT) {
              updateCurrentIndex(Utils.getString('home__register'),
                  PsConst.REQUEST_CODE__MENU_REGISTER_FRAGMENT);
            } else if (currentIndex ==
                PsConst.REQUEST_CODE__DASHBOARD_VERIFY_EMAIL_FRAGMENT) {
              updateCurrentIndex(Utils.getString('home__register'),
                  PsConst.REQUEST_CODE__DASHBOARD_REGISTER_FRAGMENT);
            } else if (currentIndex ==
                PsConst.REQUEST_CODE__DASHBOARD_SELECT_WHICH_USER_FRAGMENT) {
              updateCurrentIndex(Utils.getString('home__register'),
                  PsConst.REQUEST_CODE__DASHBOARD_REGISTER_FRAGMENT);
            } else if (currentIndex ==
                PsConst.REQUEST_CODE__MENU_SELECT_WHICH_USER_FRAGMENT) {
              updateCurrentIndex(Utils.getString('home__register'),
                  PsConst.REQUEST_CODE__MENU_REGISTER_FRAGMENT);
            }
          },
        ));
  }
}

class _DrawerMenuWidget extends StatefulWidget {
  const _DrawerMenuWidget({
    Key? key,
    required this.icon,
    required this.title,
    required this.onTap,
    required this.index,
  }) : super(key: key);

  final IconData icon;
  final String title;
  final Function onTap;
  final int index;

  @override
  __DrawerMenuWidgetState createState() => __DrawerMenuWidgetState();
}

class __DrawerMenuWidgetState extends State<_DrawerMenuWidget> {
  @override
  Widget build(BuildContext context) {
    return ListTile(
        leading: Icon(widget.icon, color: PsColors.mainColorWithWhite),
        title: Text(
          widget.title,
          style: Theme.of(context).textTheme.bodyMedium,
        ),
        onTap: () {
          widget.onTap(widget.title, widget.index);
        });
  }
}

class _DrawerHeaderWidget extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return DrawerHeader(
      child: Column(
        children: <Widget>[
          Image.asset(
            'assets/images/arm.png',
            width: PsDimens.space100,
            height: PsDimens.space72,
          ),
          const SizedBox(
            height: PsDimens.space8,
          ),
          Text(
            Utils.getString('app_name'),
            style: Theme.of(context)
                .textTheme
                .titleMedium!
                .copyWith(color: PsColors.white),
          ),
        ],
      ),
      decoration: BoxDecoration(color: PsColors.mainColor),
    );
  }
}
