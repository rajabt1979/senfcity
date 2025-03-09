// import 'package:admob_flutter/admob_flutter.dart';
import 'dart:io';

import 'package:flutter/material.dart';
import 'package:fluttercity/api/common/ps_status.dart';
import 'package:fluttercity/config/ps_colors.dart';
import 'package:fluttercity/config/ps_config.dart';
import 'package:fluttercity/constant/ps_constants.dart';
import 'package:fluttercity/constant/ps_dimens.dart';
import 'package:fluttercity/constant/route_paths.dart';
import 'package:fluttercity/provider/blog/blog_provider.dart';
import 'package:fluttercity/provider/category/category_provider.dart';
import 'package:fluttercity/provider/city_info/city_info_provider.dart';
import 'package:fluttercity/provider/item/discount_item_provider.dart';
import 'package:fluttercity/provider/item/feature_item_provider.dart';
import 'package:fluttercity/provider/item/search_item_provider.dart';
import 'package:fluttercity/provider/item/trending_item_provider.dart';
import 'package:fluttercity/provider/itemcollection/item_collection_provider.dart';
import 'package:fluttercity/repository/Common/notification_repository.dart';
import 'package:fluttercity/repository/blog_repository.dart';
import 'package:fluttercity/repository/category_repository.dart';
import 'package:fluttercity/repository/city_info_repository.dart';
import 'package:fluttercity/repository/item_collection_repository.dart';
import 'package:fluttercity/repository/item_repository.dart';
import 'package:fluttercity/ui/category/item/category_horizontal_list_item.dart';
import 'package:fluttercity/ui/common/dialog/confirm_dialog_view.dart';
import 'package:fluttercity/ui/common/dialog/error_dialog.dart';
import 'package:fluttercity/ui/common/dialog/noti_dialog.dart';
import 'package:fluttercity/ui/common/dialog/rating_dialog/core.dart';
import 'package:fluttercity/ui/common/dialog/rating_dialog/style.dart';
import 'package:fluttercity/ui/common/ps_admob_banner_widget.dart';
import 'package:fluttercity/ui/common/ps_frame_loading_widget.dart';
import 'package:fluttercity/ui/common/ps_ui_widget.dart';
import 'package:fluttercity/ui/dashboard/home/blog_slider.dart';
import 'package:fluttercity/ui/items/item/item_horizontal_list_item.dart';
import 'package:fluttercity/utils/utils.dart';
import 'package:fluttercity/viewobject/blog.dart';
import 'package:fluttercity/viewobject/common/ps_value_holder.dart';
import 'package:fluttercity/viewobject/holder/category_parameter_holder.dart';
import 'package:fluttercity/viewobject/holder/intent_holder/collection_intent_holder.dart';
import 'package:fluttercity/viewobject/holder/intent_holder/item_detail_intent_holder.dart';
import 'package:fluttercity/viewobject/holder/intent_holder/item_entry_intent_holder.dart';
import 'package:fluttercity/viewobject/holder/intent_holder/item_list_intent_holder.dart';
import 'package:fluttercity/viewobject/holder/item_parameter_holder.dart';
import 'package:fluttercity/viewobject/holder/touch_count_parameter_holder.dart';
import 'package:fluttercity/viewobject/item.dart';
import 'package:fluttercity/viewobject/item_collection_header.dart';
import 'package:fluttertoast/fluttertoast.dart';
import 'package:google_mobile_ads/google_mobile_ads.dart';
import 'package:provider/provider.dart';
import 'package:provider/single_child_widget.dart';
import 'package:shimmer/shimmer.dart';

class HomeDashboardViewWidget extends StatefulWidget {
  const HomeDashboardViewWidget(this.animationController, this.context,
      this.animationControllerForFab, this.onNotiClicked);

  final AnimationController animationController;
  final AnimationController animationControllerForFab;
  final BuildContext context;

  final Function onNotiClicked;

  @override
  _HomeDashboardViewWidgetState createState() =>
      _HomeDashboardViewWidgetState();
}

class _HomeDashboardViewWidgetState extends State<HomeDashboardViewWidget> {
  PsValueHolder? valueHolder;
  CategoryRepository? categoryRepo;
  ItemRepository? itemRepo;
  BlogRepository? blogRepo;
  ItemCollectionRepository? itemCollectionRepo;
  CityInfoRepository? shopInfoRepository;
  NotificationRepository ?notificationRepository;
  CategoryProvider? _categoryProvider;
  BlogProvider? _blogProvider;
  SearchItemProvider? _searchItemProvider;
  DiscountItemProvider? _discountItemProvider;
  TrendingItemProvider? _trendingItemProvider;
  FeaturedItemProvider? _featuredItemProvider;
  ItemCollectionProvider? _itemCollectionProvider;

  final int count = 8;
  final CategoryParameterHolder trendingCategory = CategoryParameterHolder();
  // final CategoryParameterHolder categoryIconList = CategoryParameterHolder();

  final RateMyApp _rateMyApp = RateMyApp(
      preferencesPrefix: 'rateMyApp_',
      minDays: 0,
      minLaunches: 1,
      remindDays: 5,
      remindLaunches: 1);

  @override
  void initState() {
    super.initState();
    if (_categoryProvider != null) {
      // _categoryProvider.loadCategoryList(categoryIconList.toMap());
    }

    if (Platform.isAndroid) {
      _rateMyApp.init().then((_) {
        if (_rateMyApp.shouldOpenDialog) {
          _rateMyApp.showStarRateDialog(
            context,
            title: Utils.getString('home__menu_drawer_rate_this_app'),
            message: Utils.getString('rating_popup_dialog_message'),
            ignoreNativeDialog: true,
            actionsBuilder: (BuildContext context, double? stars) {
              return <Widget>[
                TextButton(
                  child: Text(
                    Utils.getString('dialog__ok'),
                  ),
                  onPressed: () async {
                    if (stars != null) {
                      // _rateMyApp.save().then((void v) => Navigator.pop(context));
                      Navigator.pop(context);
                      if (stars < 1) {
                      } else if (stars >= 1 && stars <= 3) {
                        await _rateMyApp
                            .callEvent(RateMyAppEventType.laterButtonPressed);
                        await showDialog<dynamic>(
                            context: context,
                            builder: (BuildContext context) {
                              return ConfirmDialogView(
                                description:
                                    Utils.getString('rating_confirm_message'),
                                leftButtonText:
                                    Utils.getString('dialog__cancel'),
                                rightButtonText: Utils.getString(
                                    'home__menu_drawer_contact_us'),
                                onAgreeTap: () {
                                  Navigator.pop(context);
                                  Navigator.pushNamed(
                                    context,
                                    RoutePaths.contactUs,
                                  );
                                },
                              );
                            });
                      } else if (stars >= 4) {
                        await _rateMyApp
                            .callEvent(RateMyAppEventType.rateButtonPressed);
                        if (Platform.isIOS) {
                          Utils.launchAppStoreURL(
                              iOSAppId: PsConfig.iOSAppStoreId,
                              writeReview: true);
                        } else {
                          Utils.launchURL();
                        }
                      }
                    } else {
                      Navigator.pop(context);
                    }
                  },
                )
              ];
            },
            onDismissed: () =>
                _rateMyApp.callEvent(RateMyAppEventType.laterButtonPressed),
            dialogStyle: const DialogStyle(
              titleAlign: TextAlign.center,
              messageAlign: TextAlign.center,
              messagePadding: EdgeInsets.only(bottom: 16.0),
            ),
            starRatingOptions: const StarRatingOptions(),
          );
        }
      });
    }
  }

  Future<void> onSelectNotification(String payload) async {
    // ignore: unnecessary_null_comparison
    if (context == null) {
      widget.onNotiClicked(payload);
    } else {
      return showDialog<dynamic>(
        context: context,
        builder: (_) {
          return NotiDialog(message: '$payload');
        },
      );
    }
  }

  // Future<void> _saveDeviceToken(
  //     FirebaseMessaging _fcm, NotificationProvider notificationProvider) async {
  //   // Get the current user

  //   // Get the token for this device
  //   final String fcmToken = await _fcm.getToken();
  //   await notificationProvider.replaceNotiToken(fcmToken);

  //   final NotiRegisterParameterHolder notiRegisterParameterHolder =
  //       NotiRegisterParameterHolder(
  //           platformName: PsConst.PLATFORM,
  //           deviceId: fcmToken,
  //           loginUserId:
  //               Utils.checkUserLoginId(notificationProvider.psValueHolder));
  //   print('Token Key $fcmToken');
  //   if (fcmToken != null) {
  //     await notificationProvider
  //         .rawRegisterNotiToken(notiRegisterParameterHolder.toMap());
  //   }
  //   return true;
  // }

  @override
  Widget build(BuildContext context) {
    categoryRepo = Provider.of<CategoryRepository>(context);
    itemRepo = Provider.of<ItemRepository>(context);
    blogRepo = Provider.of<BlogRepository>(context);
    itemCollectionRepo = Provider.of<ItemCollectionRepository>(context);
    shopInfoRepository = Provider.of<CityInfoRepository>(context);
    notificationRepository = Provider.of<NotificationRepository>(context);
    valueHolder = Provider.of<PsValueHolder?>(context);

    return MultiProvider(
        providers: <SingleChildWidget>[
          ChangeNotifierProvider<CityInfoProvider>(
              lazy: false,
              create: (BuildContext context) {
                final CityInfoProvider provider = CityInfoProvider(
                  repo: shopInfoRepository,
                  psValueHolder: valueHolder,
                  // ownerCode: 'HomeDashboardViewWidget'
                );
                provider.loadCityInfo();

                return provider;
              }),
          ChangeNotifierProvider<CategoryProvider>(
              lazy: false,
              create: (BuildContext context) {
                _categoryProvider ??= CategoryProvider(
                    repo: categoryRepo,
                    psValueHolder: valueHolder,
                    limit: PsConfig.CATEGORY_LOADING_LIMIT);
                _categoryProvider!
                    .loadCategoryList(
                        _categoryProvider!.categoryParameterHolder.toMap(),
                        Utils.checkUserLoginId(_categoryProvider!.psValueHolder!))
                    .then((dynamic value) {
                  // Utils.psPrint("Is Has Internet " + value);
                  final bool isConnectedToIntenet = value ?? bool;
                  if (!isConnectedToIntenet) {
                    Fluttertoast.showToast(
                        msg: 'اتصال اینترنت برقرار نمی باشد لطفاً مجدداً تلاش کنید !',
                        toastLength: Toast.LENGTH_LONG,
                        gravity: ToastGravity.BOTTOM,
                        timeInSecForIosWeb: 3,
                        backgroundColor: Colors.blueGrey,
                        textColor: Colors.white);
                  }
                });
                return _categoryProvider!;
              }),
          // ChangeNotifierProvider<TrendingCategoryProvider>(
          //     lazy: false,
          //     create: (BuildContext context) {
          //       final TrendingCategoryProvider provider =
          //           TrendingCategoryProvider(
          //               repo: categoryRepo,
          //               psValueHolder: valueHolder,
          //               limit: PsConfig.CATEGORY_LOADING_LIMIT);
          //       provider.loadTrendingCategoryList(trendingCategory.toMap());
          //       return provider;
          //     }),
          ChangeNotifierProvider<BlogProvider>(
              lazy: false,
              create: (BuildContext context) {
                _blogProvider = BlogProvider(repo: blogRepo);
                _blogProvider!.loadBlogList();

                return _blogProvider!;
              }),
          ChangeNotifierProvider<SearchItemProvider>(
              lazy: false,
              create: (BuildContext context) {
                _searchItemProvider = SearchItemProvider(
                    repo: itemRepo,
                    limit: PsConfig.LATEST_PRODUCT_LOADING_LIMIT);
                _searchItemProvider!.loadItemListByKey(
                    ItemParameterHolder().getLatestParameterHolder());
                return _searchItemProvider!;
              }),
          ChangeNotifierProvider<DiscountItemProvider>(
              lazy: false,
              create: (BuildContext context) {
                _discountItemProvider = DiscountItemProvider(
                    repo: itemRepo,
                    limit: PsConfig.DISCOUNT_PRODUCT_LOADING_LIMIT);
                _discountItemProvider!.loadItemList();
                return _discountItemProvider!;
              }),
          ChangeNotifierProvider<TrendingItemProvider>(
              lazy: false,
              create: (BuildContext context) {
                _trendingItemProvider = TrendingItemProvider(
                    repo: itemRepo,
                    limit: PsConfig.TRENDING_PRODUCT_LOADING_LIMIT);
                _trendingItemProvider!.loadItemList(PsConst.PROPULAR_ITEM_COUNT);
                return _trendingItemProvider!;
              }),
          ChangeNotifierProvider<FeaturedItemProvider>(
              lazy: false,
              create: (BuildContext context) {
                _featuredItemProvider = FeaturedItemProvider(
                    repo: itemRepo,
                    limit: PsConfig.FEATURE_PRODUCT_LOADING_LIMIT);
                _featuredItemProvider!.loadItemList();
                return _featuredItemProvider!;
              }),
          ChangeNotifierProvider<ItemCollectionProvider>(
              lazy: false,
              create: (BuildContext context) {
                _itemCollectionProvider = ItemCollectionProvider(
                    repo: itemCollectionRepo,
                    limit: PsConfig.COLLECTION_PRODUCT_LOADING_LIMIT);
                _itemCollectionProvider!.loadItemCollectionList();
                return _itemCollectionProvider!;
              }),
        ],
        child: Scaffold(
            floatingActionButton: FadeTransition(
              opacity: widget.animationControllerForFab,
              child: ScaleTransition(
                scale: widget.animationControllerForFab,
                child: FloatingActionButton(
                  onPressed: () async {
                    if (await Utils.checkInternetConnectivity()) {
                      Utils.navigateOnUserVerificationView(context, () async {
                        Navigator.pushNamed(context, RoutePaths.itemEntry,
                            arguments: ItemEntryIntentHolder(
                                flag: PsConst.ADD_NEW_ITEM, item: Item()));
                      });
                    } else {
                      showDialog<dynamic>(
                          context: context,
                          builder: (BuildContext context) {
                            return ErrorDialog(
                              message:
                                  Utils.getString('error_dialog__no_internet'),
                            );
                          });
                    }
                  },
                  child: Icon(Icons.add, color: PsColors.white),
                  backgroundColor: PsColors.mainColor,
                  // label: Text(Utils.getString(context, 'dashboard__submit_ad'),
                  //     style: Theme.of(context)
                  //         .textTheme
                  //         .caption
                  //         .copyWith(color: PsColors.white)),
                ),
              ),
            ),
            body: Container(
              color: PsColors.coreBackgroundColor,
              child: RefreshIndicator(
                onRefresh: () {
                  _categoryProvider!
                      .resetCategoryList(
                          _categoryProvider!.categoryParameterHolder.toMap(),
                          Utils.checkUserLoginId(
                              _categoryProvider!.psValueHolder!))
                      .then((dynamic value) {
                    // Utils.psPrint("Is Has Internet " + value);
                    final bool isConnectedToIntenet = value ?? bool;
                    if (!isConnectedToIntenet) {
                      Fluttertoast.showToast(
                          msg: 'No Internet Connectiion. Please try again !',
                          toastLength: Toast.LENGTH_LONG,
                          gravity: ToastGravity.BOTTOM,
                          timeInSecForIosWeb: 1,
                          backgroundColor: Colors.blueGrey,
                          textColor: Colors.white);
                    }
                  });
                  _blogProvider!.resetBlogList();
                  _searchItemProvider!.resetLatestItemList(
                      ItemParameterHolder().getLatestParameterHolder());
                  _discountItemProvider!.resetDiscountItemList();
                  _trendingItemProvider!.resetTrendingItemList();
                  _featuredItemProvider!.resetFeatureItemList();
                  return _itemCollectionProvider!.resetItemCollectionList();
                },
                child: CustomScrollView(
                  scrollDirection: Axis.vertical,
                  slivers: <Widget>[
                    _CityInfoWidget(
                      animationController: widget.animationController,
                      animation: Tween<double>(begin: 0.0, end: 1.0).animate(
                          CurvedAnimation(
                              parent: widget.animationController,
                              curve: Interval((1 / count) * 1, 1.0,
                                  curve: Curves.fastOutSlowIn))), //animation
                    ),
                    _HomeBlogSliderWidget(
                      animationController: widget.animationController,

                      animation: Tween<double>(begin: 0.0, end: 1.0).animate(
                          CurvedAnimation(
                              parent: widget.animationController,
                              curve: Interval((1 / count) * 5, 1.0,
                                  curve: Curves.fastOutSlowIn))), //animation
                    ),
                    ///
                    /// category List Widget
                    ///
                    _HomeCategoryHorizontalListWidget(
                      psValueHolder: valueHolder!,
                      animationController: widget.animationController,
                      animation: Tween<double>(begin: 0.0, end: 1.0).animate(
                          CurvedAnimation(
                              parent: widget.animationController,
                              curve: Interval((1 / count) * 2, 1.0,
                                  curve: Curves.fastOutSlowIn))), //animation
                    ),

                    // _DiscountItemHorizontalListWidget(
                    //   animationController: widget.animationController,
                    //   //animationController,
                    //   animation: Tween<double>(begin: 0.0, end: 1.0).animate(
                    //       CurvedAnimation(
                    //           parent: widget.animationController,
                    //           curve: Interval((1 / count) * 3, 1.0,
                    //               curve: Curves.fastOutSlowIn))), //animation
                    // ),

                    _HomeFeaturedItemHorizontalListWidget(
                      animationController: widget.animationController,
                      //animationController,
                      animation: Tween<double>(begin: 0.0, end: 1.0).animate(
                          CurvedAnimation(
                              parent: widget.animationController,
                              curve: Interval((1 / count) * 3, 1.0,
                                  curve: Curves.fastOutSlowIn))),
                    ),
                    const SliverToBoxAdapter(
                      child: PsAdMobBannerWidget(admobSize: AdSize.fullBanner,),
                    ),
                    // _HomeSelectingItemTypeWidget(
                    //   animationController: widget.animationController,
                    //   //animationController,
                    //   animation: Tween<double>(begin: 0.0, end: 1.0).animate(
                    //       CurvedAnimation(
                    //           parent: widget.animationController,
                    //           curve: Interval((1 / count) * 5, 1.0,
                    //               curve: Curves.fastOutSlowIn))),
                    // ),
                    // _HomeTrendingCategoryHorizontalListWidget(
                    //   psValueHolder: valueHolder,
                    //   animationController: widget.animationController,
                    //   //animationController,
                    //   animation: Tween<double>(begin: 0.0, end: 1.0).animate(
                    //       CurvedAnimation(
                    //           parent: widget.animationController,
                    //           curve: Interval((1 / count) * 6, 1.0,
                    //               curve: Curves.fastOutSlowIn))), //animation
                    // ),
                    _HomeTrendingItemHorizontalListWidget(
                      animationController: widget.animationController,
                      //animationController,
                      animation: Tween<double>(begin: 0.0, end: 1.0).animate(
                          CurvedAnimation(
                              parent: widget.animationController,
                              curve: Interval((1 / count) * 6, 1.0,
                                  curve: Curves.fastOutSlowIn))), //animation
                    ),
                    _HomeLatestItemHorizontalListWidget(
                      animationController: widget.animationController,
                      //animationController,
                      animation: Tween<double>(begin: 0.0, end: 1.0).animate(
                          CurvedAnimation(
                              parent: widget.animationController,
                              curve: Interval((1 / count) * 4, 1.0,
                                  curve: Curves.fastOutSlowIn))), //animation
                    ),

                    // _HomeBlogSliderWidget(
                    //   animationController: widget.animationController,
                    //
                    //   animation: Tween<double>(begin: 0.0, end: 1.0).animate(
                    //       CurvedAnimation(
                    //           parent: widget.animationController,
                    //           curve: Interval((1 / count) * 5, 1.0,
                    //               curve: Curves.fastOutSlowIn))), //animation
                    // ),
                    const SliverToBoxAdapter(
                      child: PsAdMobBannerWidget(admobSize: AdSize.banner,),
                    ),

                    _HomeCollectionItemListWidget(
                      animationController: widget.animationController,

                      animation: Tween<double>(begin: 0.0, end: 1.0).animate(
                          CurvedAnimation(
                              parent: widget.animationController,
                              curve: Interval((1 / count) * 7, 1.0,
                                  curve: Curves.fastOutSlowIn))), //animation
                    ),
                  ],
                ),
              ),
            )));
  }
}

class _HomeLatestItemHorizontalListWidget extends StatefulWidget {
  const _HomeLatestItemHorizontalListWidget({
    Key? key,
    required this.animationController,
    required this.animation,
  }) : super(key: key);

  final AnimationController animationController;
  final Animation<double> animation;

  @override
  __HomeLatestItemHorizontalListWidgetState createState() =>
      __HomeLatestItemHorizontalListWidgetState();
}

class __HomeLatestItemHorizontalListWidgetState
    extends State<_HomeLatestItemHorizontalListWidget> {
  @override
  Widget build(BuildContext context) {
    return SliverToBoxAdapter(
      child: Consumer<SearchItemProvider>(
        builder: (BuildContext context, SearchItemProvider itemProvider,
            Widget? child) {
          return AnimatedBuilder(
              animation: widget.animationController,
              child: Column(children: <Widget>[
                _MyHeaderWidget(
                  headerName: Utils.getString('dashboard__latest_item'),
                  viewAllClicked: () {
                    Navigator.pushNamed(context, RoutePaths.filterItemList,
                        arguments: ItemListIntentHolder(
                          appBarTitle:
                              Utils.getString('dashboard__latest_item'),
                          itemParameterHolder:
                              ItemParameterHolder().getLatestParameterHolder(),
                        ));
                  },
                ),
                Container(
                  alignment: Alignment.centerLeft,
                  padding: const EdgeInsets.only(left: PsDimens.space16),
                  child: Text(
                    Utils.getString('dashboard__latest_item_description'),
                    textAlign: TextAlign.start,
                    style: Theme.of(context).textTheme.bodyLarge,
                  ),
                ),
                Container(
                    height: PsDimens.space320,
                    width: MediaQuery.of(context).size.width,
                    child: ListView.builder(
                        scrollDirection: Axis.horizontal,
                        padding: const EdgeInsets.only(left: PsDimens.space16),
                        itemCount: itemProvider.itemList.data!.length,
                        itemBuilder: (BuildContext context, int index) {
                          if (itemProvider.itemList.status ==
                              PsStatus.BLOCK_LOADING) {
                            return Shimmer.fromColors(
                                baseColor: PsColors.grey,
                                highlightColor: PsColors.white,
                                child: Row(children: const <Widget>[
                                  PsFrameUIForLoading(),
                                ]));
                          } else {
                            final Item item = itemProvider.itemList.data![index];

                            return ItemHorizontalListItem(
                              coreTagKey: itemProvider.hashCode.toString() +
                                  item.id!, //'latest',
                              item: item,
                              onTap: () async {
                                print(item.defaultPhoto!.imgPath);

                                final ItemDetailIntentHolder holder =
                                    ItemDetailIntentHolder(
                                  itemId: item.id,
                                  heroTagImage: '',
                                  heroTagTitle: '',
                                  heroTagOriginalPrice: '',
                                  heroTagUnitPrice: '',
                                );

                                final dynamic result =
                                    await Navigator.pushNamed(
                                        context, RoutePaths.itemDetail,
                                        arguments: holder);
                                if (result == null) {
                                  itemProvider.resetLatestItemList(
                                      ItemParameterHolder()
                                          .getLatestParameterHolder());
                                }
                              },
                            );
                          }
                        }))
              ]),
              // : Container(),
              builder: (BuildContext context, Widget? child) {
                if (itemProvider.itemList.data != null &&
                    itemProvider.itemList.data!.isNotEmpty) {
                  return FadeTransition(
                    opacity: widget.animation,
                    child: Transform(
                      transform: Matrix4.translationValues(
                          0.0, 100 * (1.0 - widget.animation.value), 0.0),
                      child: child,
                    ),
                  );
                } else {
                  return Container();
                }
              });
        },
      ),
    );
  }
}

class _HomeBlogSliderWidget extends StatelessWidget {
  const _HomeBlogSliderWidget({
    Key? key,
    required this.animationController,
    required this.animation,
  }) : super(key: key);

  final AnimationController animationController;
  final Animation<double> animation;

  @override
  Widget build(BuildContext context) {
    // const int count = 5;
    // final Animation<double> animation = Tween<double>(begin: 0.0, end: 1.0)
    //     .animate(CurvedAnimation(
    //         parent: animationController,
    //         curve: const Interval((1 / count) * 1, 1.0,
    //             curve: Curves.fastOutSlowIn)));

    return SliverToBoxAdapter(
      child: Consumer<BlogProvider>(builder:
          (BuildContext context, BlogProvider blogProvider, Widget? child) {
        return AnimatedBuilder(
            animation: animationController,
            child: (
                    blogProvider.blogList.data!.isNotEmpty)
                ? Column(
                    mainAxisSize: MainAxisSize.min,
                    children: <Widget>[
                      _MyHeaderWidget(
                        headerName: Utils.getString('dashboard__blog_item'),
                        viewAllClicked: () {
                          Navigator.pushNamed(
                            context,
                            RoutePaths.blogList,
                          );
                        },
                      ),
                      Container(
                        // decoration: BoxDecoration(
                        //   boxShadow: <BoxShadow>[
                        //     BoxShadow(
                        //         color: PsColors.mainLightShadowColor,
                        //         offset: const Offset(1.1, 1.1),
                        //         blurRadius: PsDimens.space8),
                        //   ],
                        // ),
                        // margin: const EdgeInsets.only(
                        //     top: PsDimens.space8,
                        //     bottom: PsDimens.space20),
                        width: double.infinity,
                        child: BlogSliderView(
                          blogList: blogProvider.blogList.data!,
                          onTap: (Blog blog) {
                            Navigator.pushNamed(
                              context,
                              RoutePaths.blogDetail,
                              arguments: blog,
                            );
                          },
                        ),
                      ),
                      // const PsAdMobBannerWidget(),
                    ],
                  )
                : Container(),
            builder: (BuildContext context, Widget? child) {
              return FadeTransition(
                  opacity: animation,
                  child: Transform(
                    transform: Matrix4.translationValues(
                        0.0, 100 * (1.0 - animation.value), 0.0),
                    child: child,
                  ));
            });
      }),
    );
  }
}

class _HomeFeaturedItemHorizontalListWidget extends StatefulWidget {
  const _HomeFeaturedItemHorizontalListWidget({
    Key? key,
    required this.animationController,
    required this.animation,
  }) : super(key: key);

  final AnimationController animationController;
  final Animation<double> animation;

  @override
  __HomeFeaturedItemHorizontalListWidgetState createState() =>
      __HomeFeaturedItemHorizontalListWidgetState();
}

class __HomeFeaturedItemHorizontalListWidgetState
    extends State<_HomeFeaturedItemHorizontalListWidget> {
  @override
  Widget build(BuildContext context) {
    return SliverToBoxAdapter(
      child: Consumer<FeaturedItemProvider>(
        builder: (BuildContext context, FeaturedItemProvider itemProvider,
            Widget ?child) {
          return AnimatedBuilder(
            animation: widget.animationController,
            child: (itemProvider.itemList.data != null &&
                    itemProvider.itemList.data!.isNotEmpty)
                ? Column(
                    children: <Widget>[
                      Container(
                        color: Utils.isLightMode(context)
                            ? Colors.yellow[50]
                            : Colors.black12,
                        child: Column(
                          children: <Widget>[
                            _MyHeaderWidget(
                              headerName:
                                  Utils.getString('dashboard__feature_item'),
                              viewAllClicked: () {
                                Navigator.pushNamed(
                                    context, RoutePaths.filterItemList,
                                    arguments: ItemListIntentHolder(
                                        appBarTitle: Utils.getString(
                                            'dashboard__feature_item'),
                                        itemParameterHolder:
                                            ItemParameterHolder()
                                                .getFeaturedParameterHolder()));
                              },
                            ),
                            Container(
                              alignment: Alignment.centerLeft,
                              padding:
                                  const EdgeInsets.only(left: PsDimens.space16),
                              child: Text(
                                Utils.getString(
                                    'dashbord__feature_item_description'),
                                textAlign: TextAlign.start,
                                style: Theme.of(context).textTheme.bodyLarge,
                              ),
                            ),
                            Container(
                                height: PsDimens.space300,
                                color: Utils.isLightMode(context)
                                    ? Colors.yellow[50]
                                    : Colors.black12,
                                width: MediaQuery.of(context).size.width,
                                child: ListView.builder(
                                    scrollDirection: Axis.horizontal,
                                    padding: const EdgeInsets.only(
                                        left: PsDimens.space16),
                                    itemCount:
                                        itemProvider.itemList.data!.length,
                                    itemBuilder:
                                        (BuildContext context, int index) {
                                      if (itemProvider.itemList.status ==
                                          PsStatus.BLOCK_LOADING) {
                                        return Shimmer.fromColors(
                                            baseColor: PsColors.grey,
                                            highlightColor: PsColors.white,
                                            child: Row(children: const <Widget>[
                                              PsFrameUIForLoading(),
                                            ]));
                                      } else {
                                        final Item item =
                                            itemProvider.itemList.data![index];
                                        return ItemHorizontalListItem(
                                          coreTagKey:
                                              itemProvider.hashCode.toString() +
                                                  item.id!, //'feature',
                                          item:
                                              itemProvider.itemList.data![index],
                                          onTap: () async {
                                            print(itemProvider
                                                .itemList
                                                .data![index]
                                                .defaultPhoto!
                                                .imgPath);
                                            final ItemDetailIntentHolder
                                                holder = ItemDetailIntentHolder(
                                              itemId: item.id,
                                              heroTagImage: '',
                                              heroTagTitle: '',
                                              heroTagOriginalPrice: '',
                                              heroTagUnitPrice: '',
                                            );

                                            final dynamic result =
                                                await Navigator.pushNamed(
                                                    context,
                                                    RoutePaths.itemDetail,
                                                    arguments: holder);
                                            if (result == null) {
                                              itemProvider
                                                  .resetFeatureItemList();
                                            }
                                          },
                                        );
                                      }
                                    }))
                          ],
                        ),
                      ),
                      // const PsAdMobBannerWidget(),
                    ],
                  )
                : Container(),
            builder: (BuildContext context, Widget? child) {
              return FadeTransition(
                opacity: widget.animation,
                child: Transform(
                  transform: Matrix4.translationValues(
                      0.0, 100 * (1.0 - widget.animation.value), 0.0),
                  child: child,
                ),
              );
            },
          );
        },
      ),
    );
  }
}

class _HomeTrendingItemHorizontalListWidget extends StatefulWidget {
  const _HomeTrendingItemHorizontalListWidget({
    Key? key,
    required this.animationController,
    required this.animation,
  }) : super(key: key);

  final AnimationController animationController;
  final Animation<double> animation;

  @override
  __HomeTrendingItemHorizontalListWidgetState createState() =>
      __HomeTrendingItemHorizontalListWidgetState();
}

class __HomeTrendingItemHorizontalListWidgetState
    extends State<_HomeTrendingItemHorizontalListWidget> {
  @override
  Widget build(BuildContext context) {
    return SliverToBoxAdapter(
      child: Consumer<TrendingItemProvider>(
        builder: (BuildContext context, TrendingItemProvider itemProvider,
            Widget? child) {
          return AnimatedBuilder(
            animation: widget.animationController,
            child: (itemProvider.itemList.data != null &&
                    itemProvider.itemList.data!.isNotEmpty)
                ? Column(
                    children: <Widget>[
                      _MyHeaderWidget(
                        headerName: Utils.getString('dashboard__trending_item'),
                        viewAllClicked: () {
                          Navigator.pushNamed(
                              context, RoutePaths.filterItemList,
                              arguments: ItemListIntentHolder(
                                  appBarTitle: Utils.getString(
                                      'dashboard__trending_item'),
                                  itemParameterHolder: ItemParameterHolder()
                                      .getTrendingParameterHolder()));
                        },
                      ),
                      Container(
                        alignment: Alignment.centerLeft,
                        padding: const EdgeInsets.only(left: PsDimens.space16),
                        child: Text(
                          Utils.getString(
                              'dashboard__trending_item_description'),
                          textAlign: TextAlign.start,
                          style: Theme.of(context).textTheme.bodyLarge,
                        ),
                      ),
                      CustomScrollView(
                          physics: const NeverScrollableScrollPhysics(),
                          shrinkWrap: true,
                          slivers: <Widget>[
                            SliverGrid(
                                gridDelegate:
                                    const SliverGridDelegateWithFixedCrossAxisCount(
                                        crossAxisCount: 2,
                                        childAspectRatio: 0.68),
                                delegate: SliverChildBuilderDelegate(
                                  (BuildContext context, int index) {
                                    if (itemProvider.itemList.status ==
                                        PsStatus.BLOCK_LOADING) {
                                      return Shimmer.fromColors(
                                          baseColor: PsColors.grey,
                                          highlightColor: PsColors.white,
                                          child: Row(children: const <Widget>[
                                            PsFrameUIForLoading(),
                                          ]));
                                    } else {
                                      final Item item =
                                          itemProvider.itemList.data![index];
                                      return ItemHorizontalListItem(
                                        coreTagKey:
                                            itemProvider.hashCode.toString() +
                                                item.id!,
                                        item: itemProvider.itemList.data![index],
                                        onTap: () async {
                                          print(itemProvider
                                              .itemList
                                              .data![index]
                                              .defaultPhoto!
                                              .imgPath);
                                          final ItemDetailIntentHolder holder =
                                              ItemDetailIntentHolder(
                                            itemId: item.id,
                                            heroTagImage: '',
                                            heroTagTitle: '',
                                            heroTagOriginalPrice: '',
                                            heroTagUnitPrice: '',
                                          );
                                          final dynamic result =
                                              await Navigator.pushNamed(context,
                                                  RoutePaths.itemDetail,
                                                  arguments: holder);
                                          if (result == null) {
                                            itemProvider
                                                .resetTrendingItemList();
                                          }
                                        },
                                      );
                                    }
                                  },
                                  childCount: itemProvider.itemList.data!.length,
                                ))
                          ])
                    ],
                  )
                : Container(),
            builder: (BuildContext context, Widget? child) {
              return FadeTransition(
                opacity: widget.animation,
                child: Transform(
                  transform: Matrix4.translationValues(
                      0.0, 100 * (1.0 - widget.animation.value), 0.0),
                  child: child,
                ),
              );
            },
          );
        },
      ),
    );
  }
}

class _DiscountItemHorizontalListWidget extends StatefulWidget {
  const _DiscountItemHorizontalListWidget({
    Key? key,
    required this.animationController,
    required this.animation,
  }) : super(key: key);

  final AnimationController animationController;
  final Animation<double> animation;

  @override
  __DiscountItemHorizontalListWidgetState createState() =>
      __DiscountItemHorizontalListWidgetState();
}

class __DiscountItemHorizontalListWidgetState
    extends State<_DiscountItemHorizontalListWidget> {
  bool isConnectedToInternet = false;
  bool isSuccessfullyLoaded = true;

  void checkConnection() {
    Utils.checkInternetConnectivity().then((bool onValue) {
      isConnectedToInternet = onValue;
      if (isConnectedToInternet && PsConfig.showAdMob) {
        setState(() {});
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    if (!isConnectedToInternet && PsConfig.showAdMob) {
      print('loading ads....');
      checkConnection();
    }
    return SliverToBoxAdapter(child: Consumer<DiscountItemProvider>(builder:
        (BuildContext context, DiscountItemProvider itemProvider,
            Widget? child) {
      return AnimatedBuilder(
          animation: widget.animationController,
          child: (itemProvider.itemList.data != null &&
                  itemProvider.itemList.data!.isNotEmpty)
              ? Column(children: <Widget>[
                  _MyHeaderWidget(
                    headerName: Utils.getString('dashboard__discount_item'),
                    viewAllClicked: () {
                      Navigator.pushNamed(context, RoutePaths.filterItemList,
                          arguments: ItemListIntentHolder(
                              appBarTitle:
                                  Utils.getString('dashboard__discount_item'),
                              itemParameterHolder: ItemParameterHolder()
                                  .getDiscountParameterHolder()));
                    },
                  ),
                  Container(
                      height: PsDimens.space320,
                      width: MediaQuery.of(context).size.width,
                      child: ListView.builder(
                          scrollDirection: Axis.horizontal,
                          padding:
                              const EdgeInsets.only(left: PsDimens.space16),
                          itemCount: itemProvider.itemList.data!.length,
                          itemBuilder: (BuildContext context, int index) {
                            if (itemProvider.itemList.status ==
                                PsStatus.BLOCK_LOADING) {
                              return Shimmer.fromColors(
                                  baseColor: PsColors.grey,
                                  highlightColor: PsColors.white,
                                  child: Row(children: const <Widget>[
                                    PsFrameUIForLoading(),
                                  ]));
                            } else {
                              final Item item =
                                  itemProvider.itemList.data![index];
                              return ItemHorizontalListItem(
                                coreTagKey:
                                    itemProvider.hashCode.toString() + item.id!,
                                item: itemProvider.itemList.data![index],
                                onTap: () async {
                                  print(itemProvider.itemList.data![index]
                                      .defaultPhoto!.imgPath);
                                  final ItemDetailIntentHolder holder =
                                      ItemDetailIntentHolder(
                                    itemId: item.id,
                                    heroTagImage: '',
                                    heroTagTitle: '',
                                    heroTagOriginalPrice: '',
                                    heroTagUnitPrice: '',
                                  );
                                  final dynamic result =
                                      await Navigator.pushNamed(
                                          context, RoutePaths.itemDetail,
                                          arguments: holder);
                                  if (result == null) {
                                    itemProvider.resetDiscountItemList();
                                  }
                                },
                              );
                            }
                          })),
                  const PsAdMobBannerWidget(
                    // admobBannerSize: AdmobBannerSize.MEDIUM_RECTANGLE,
                    admobSize: AdSize.fullBanner,
                  ),
                  // Visibility(
                  //   visible: PsConfig.showAdMob &&
                  //       isSuccessfullyLoaded &&
                  //       isConnectedToInternet,
                  //   child: AdmobBanner(
                  //     adUnitId: Utils.getBannerAdUnitId(),
                  //     adSize: AdmobBannerSize.MEDIUM_RECTANGLE,
                  //     listener: (AdmobAdEvent event,
                  //         Map<String, dynamic> map) {
                  //       print('BannerAd event is $event');
                  //       if (event == AdmobAdEvent.loaded) {
                  //         isSuccessfullyLoaded = true;
                  //       } else {
                  //         isSuccessfullyLoaded = false;
                  //         setState(() {});
                  //       }
                  //     },
                  //   ),
                  // ),
                ])
              : Container(),
          builder: (BuildContext context, Widget? child) {
            return FadeTransition(
                opacity: widget.animation,
                child: Transform(
                  transform: Matrix4.translationValues(
                      0.0, 100 * (1.0 - widget.animation.value), 0.0),
                  child: child,
                ));
          });
    }));
  }
}

class _CityInfoWidget extends StatelessWidget {
  const _CityInfoWidget({
    Key? key,
    required this.animationController,
    required this.animation,
  }) : super(
          key: key,
        );

  final AnimationController animationController;
  final Animation<double> animation;
  @override
  Widget build(BuildContext context) {
    // const int count = 6;
    // final Animation<double> animation = Tween<double>(begin: 0.0, end: 1.0)
    //     .animate(CurvedAnimation(
    //         parent: animationController,
    //         curve: const Interval((1 / count) * 1, 1.0,
    //             curve: Curves.fastOutSlowIn)));
    return SliverToBoxAdapter(
      child: Consumer<CityInfoProvider>(builder: (BuildContext context,
          CityInfoProvider cityInfoProvider, Widget? child) {
        return AnimatedBuilder(
            animation: animationController,
            child: (
                    cityInfoProvider.cityInfo.data != null)
                ? Column(children: <Widget>[
                    PsNetworkImage(
                      photoKey: '',
                      defaultPhoto: cityInfoProvider.cityInfo.data!.defaultPhoto!,
                      width: double.infinity,
                      height: 220,
                      onTap: () {
                        Utils.psPrint(cityInfoProvider
                            .cityInfo.data!.defaultPhoto!.imgParentId!);
                      },
                    ),
                    // const SizedBox(
                    //   height: PsDimens.space12,
                    // ),
                    // Row(
                    //   mainAxisAlignment: MainAxisAlignment.end,
                    //   children: <Widget>[
                    //     const SizedBox(
                    //       width: PsDimens.space16,
                    //     ),

                    //     const SizedBox(
                    //       width: PsDimens.space16,
                    //     ),
                    //   ],
                    // ),
                    Container(
                        width: double.infinity,
                        padding: const EdgeInsets.all(PsDimens.space16),
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.end,
                          mainAxisSize: MainAxisSize.max,
                          children: <Widget>[
                            Container(
                              width: double.infinity,
                              child: Text(cityInfoProvider.cityInfo.data!.name!,
                                  textAlign: TextAlign.start,
                                  style: Theme.of(context)
                                      .textTheme
                                      .titleLarge!
                                      .copyWith(fontWeight: FontWeight.bold)),
                            ),
                            const SizedBox(
                              height: PsDimens.space12,
                            ),
                            Container(
                              width: double.infinity,
                              child: Text(
                                cityInfoProvider.cityInfo.data!.description ??
                                    '',
                                textAlign: TextAlign.justify,
                                maxLines: 4,
                                overflow: TextOverflow.ellipsis,
                                style: Theme.of(context )
                                    .textTheme
                                    .bodyLarge!
                                    .copyWith(height: 1.5),
                              ),
                            ),
                          ],
                        )),
                    InkWell(
                      onTap: () {
                        Navigator.pushNamed(
                            context, RoutePaths.cityInfoContainerView,
                            arguments: cityInfoProvider.cityInfo.data);
                      },
                      child: Text(
                        Utils.getString('dashboard__about_city'),
                        style: Theme.of(context)
                            .textTheme
                            .titleMedium!
                            .copyWith(color: PsColors.mainColor),
                        textAlign: TextAlign.center,
                      ),
                    ),
                    const SizedBox(height: PsDimens.space20),
                    Divider(
                      height: PsDimens.space1,
                      color: Theme.of(context).iconTheme.color,
                    ),
                  ])
                : Container(),
            builder: (BuildContext context, Widget? child) {
              return FadeTransition(
                  opacity: animation,
                  child: Transform(
                    transform: Matrix4.translationValues(
                        0.0, 100 * (1.0 - animation.value), 0.0),
                    child: child,
                  ));
            });
      }),
    );
  }
}

class _HomeCollectionItemListWidget extends StatelessWidget {
  const _HomeCollectionItemListWidget({
    Key ?key,
    required this.animationController,
    required this.animation,
  }) : super(key: key);

  final AnimationController animationController;
  final Animation<double> animation;

  @override
  Widget build(BuildContext context) {
    // const int count = 5;
    // final Animation<double> animation = Tween<double>(begin: 0.0, end: 1.0)
    //     .animate(CurvedAnimation(
    //         parent: animationController,
    //         curve: const Interval((1 / count) * 1, 1.0,
    //             curve: Curves.fastOutSlowIn)));

    return SliverToBoxAdapter(
      child: Consumer<ItemCollectionProvider>(builder: (BuildContext context,
          ItemCollectionProvider collectionProvider, Widget? child) {
        return AnimatedBuilder(
            animation: animationController,
            child: (
                    collectionProvider.itemCollectionList.data!.isNotEmpty)
                ? ListView.builder(
                    scrollDirection: Axis.vertical,
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    padding: const EdgeInsets.only(left: PsDimens.space16),
                    itemCount:
                        collectionProvider.itemCollectionList.data!.length,
                    itemBuilder: (BuildContext context, int index) {
                      if (collectionProvider.itemCollectionList.status ==
                          PsStatus.BLOCK_LOADING) {
                        return Shimmer.fromColors(
                            baseColor: PsColors.grey,
                            highlightColor: PsColors.white,
                            child: Row(children: const <Widget>[
                              PsFrameUIForLoading(),
                            ]));
                      } else {
                        return _HomeCollectionItemsHorizontalListWidget(
                            headerName: collectionProvider
                                .itemCollectionList.data![index].name!,
                            collectionData: collectionProvider
                                .itemCollectionList.data![index],
                            itemCollectionProvider: collectionProvider);
                      }
                    })
                : Container(),
            builder: (BuildContext context, Widget? child) {
              return FadeTransition(
                  opacity: animation,
                  child: Transform(
                    transform: Matrix4.translationValues(
                        0.0, 100 * (1.0 - animation.value), 0.0),
                    child: child,
                  ));
            });
      }),
    );
  }
}

class _HomeCollectionItemsHorizontalListWidget extends StatefulWidget {
  const _HomeCollectionItemsHorizontalListWidget(
      {Key? key,
      required this.collectionData,
      required this.headerName,
      required this.itemCollectionProvider})
      : super(key: key);

  final ItemCollectionHeader collectionData;
  final String headerName;
  final ItemCollectionProvider itemCollectionProvider;

  @override
  __HomeCollectionItemsHorizontalListWidgetState createState() =>
      __HomeCollectionItemsHorizontalListWidgetState();
}

class __HomeCollectionItemsHorizontalListWidgetState
    extends State<_HomeCollectionItemsHorizontalListWidget> {
  @override
  Widget build(BuildContext context) {
    if (widget.collectionData.itemList != null &&
        widget.collectionData.itemList!.isNotEmpty) {
      return Column(
        children: <Widget>[
          _MyHeaderWidget(
            headerName: widget.headerName,
            itemCollectionHeader: widget.collectionData,
            viewAllClicked: () {
              Navigator.pushNamed(context, RoutePaths.itemListByCollectionId,
                  arguments: CollectionIntentHolder(
                    itemCollectionHeader: widget.collectionData,
                    appBarTitle: widget.headerName,
                  ));
            },
          ),
          Container(
              height: PsDimens.space320,
              width: MediaQuery.of(context).size.width,
              child: ListView.builder(
                  scrollDirection: Axis.horizontal,
                  itemCount: widget.collectionData.itemList!.length,
                  padding: const EdgeInsets.only(left: PsDimens.space16),
                  itemBuilder: (BuildContext context, int index) {
                    final Item item = widget.collectionData.itemList![index];
                    return ItemHorizontalListItem(
                      coreTagKey: '',
                      item: item,
                      onTap: () async {
                        print(item.defaultPhoto!.imgPath);
                        final ItemDetailIntentHolder holder =
                            ItemDetailIntentHolder(
                          itemId: item.id,
                          heroTagImage: '',
                          heroTagTitle: '',
                          heroTagOriginalPrice: '',
                          heroTagUnitPrice: '',
                        );
                        final dynamic result = await Navigator.pushNamed(
                            context, RoutePaths.itemDetail,
                            arguments: holder);
                        if (result == null) {
                          widget.itemCollectionProvider
                              .resetItemCollectionList();
                          // setState(() {
                          //   // item.loadItemList();
                          // });
                        }
                      },
                    );
                  }))
        ],
      );
    } else {
      return Container();
    }
  }
}

class _HomeCategoryHorizontalListWidget extends StatefulWidget {
  const _HomeCategoryHorizontalListWidget(
      {Key? key,
      required this.animationController,
      required this.animation,
      required this.psValueHolder})
      : super(key: key);

  final AnimationController animationController;
  final Animation<double> animation;
  final PsValueHolder psValueHolder;

  @override
  __HomeCategoryHorizontalListWidgetState createState() =>
      __HomeCategoryHorizontalListWidgetState();
}

class __HomeCategoryHorizontalListWidgetState
    extends State<_HomeCategoryHorizontalListWidget> {
  @override
  Widget build(BuildContext context) {
    return SliverToBoxAdapter(child: Consumer<CategoryProvider>(
      builder: (BuildContext context, CategoryProvider categoryProvider,
          Widget? child) {
        return AnimatedBuilder(
            animation: widget.animationController,
            child: (categoryProvider.categoryList.data != null &&
                    categoryProvider.categoryList.data!.isNotEmpty)
                ? Column(mainAxisAlignment: MainAxisAlignment.start, children: <
                    Widget>[
                    _MyHeaderWidget(
                      headerName: Utils.getString('dashboard__categories'),
                      viewAllClicked: () {
                        Navigator.pushNamed(context, RoutePaths.categoryList,
                            arguments:
                                Utils.getString('dashboard__categories'));
                      },
                    ),
                    Container(
                      alignment: Alignment.centerLeft,
                      padding: const EdgeInsets.only(left: PsDimens.space16),
                      child: Text(
                        Utils.getString('dashboard__category_description'),
                        textAlign: TextAlign.start,
                        style: Theme.of(context).textTheme.bodyLarge,
                      ),
                    ),
                    Container(
                      height: PsDimens.space140,
                      width: MediaQuery.of(context).size.width,
                      child: ListView.builder(
                          shrinkWrap: true,
                          padding:
                              const EdgeInsets.only(left: PsDimens.space16),
                          scrollDirection: Axis.horizontal,
                          itemCount: categoryProvider.categoryList.data!.length,
                          itemBuilder: (BuildContext context, int index) {
                            if (categoryProvider.categoryList.status ==
                                PsStatus.BLOCK_LOADING) {
                              return Shimmer.fromColors(
                                  baseColor: PsColors.grey,
                                  highlightColor: PsColors.white,
                                  child: Row(children: const <Widget>[
                                    PsFrameUIForLoading(),
                                  ]));
                            } else {
                              return CategoryHorizontalListItem(
                                category:
                                    categoryProvider.categoryList.data![index],
                                // animation: widget.animation,
                                // animationController:
                                //     widget.animationController,
                                onTap: () {
                                  final String loginUserId =
                                      Utils.checkUserLoginId(
                                          categoryProvider.psValueHolder!);

                                  final TouchCountParameterHolder
                                      touchCountParameterHolder =
                                      TouchCountParameterHolder(
                                          typeId: categoryProvider
                                              .categoryList.data![index].id,
                                          typeName: PsConst
                                              .FILTERING_TYPE_NAME_CATEGORY,
                                          userId: loginUserId);

                                  categoryProvider.postTouchCount(
                                      touchCountParameterHolder.toMap());
                                  if (PsConfig.isShowSubCategory) {
                                    Navigator.pushNamed(
                                        context, RoutePaths.subCategoryList,
                                        arguments: categoryProvider
                                            .categoryList.data![index]);
                                  } else {
                                    final ItemParameterHolder
                                        itemParameterHolder =
                                        ItemParameterHolder()
                                            .getLatestParameterHolder();
                                    itemParameterHolder.catId = categoryProvider
                                        .categoryList.data![index].id;
                                    final dynamic result = Navigator.pushNamed(
                                        context, RoutePaths.filterItemList,
                                        arguments: ItemListIntentHolder(
                                          appBarTitle: categoryProvider
                                              .categoryList.data![index].name!,
                                          itemParameterHolder:
                                              itemParameterHolder,
                                        ));
                                    if (result == null) {
                                      categoryProvider.resetCategoryList(
                                          categoryProvider
                                              .categoryParameterHolder
                                              .toMap(),
                                          Utils.checkUserLoginId(
                                              categoryProvider.psValueHolder!));
                                    }
                                  }
                                },
                              );
                            }
                          }),
                    )
                  ])
                : Container(),
            builder: (BuildContext context, Widget? child) {
              return FadeTransition(
                  opacity: widget.animation,
                  child: Transform(
                    transform: Matrix4.translationValues(
                        0.0, 30 * (1.0 - widget.animation.value), 0.0),
                    child: child,
                  ));
            });
      },
    ));
  }
}

class _MyHeaderWidget extends StatefulWidget {
  const _MyHeaderWidget({
    Key? key,
    required this.headerName,
    this.itemCollectionHeader,
    required this.viewAllClicked,
  }) : super(key: key);

  final String headerName;
  final Function viewAllClicked;
  final ItemCollectionHeader? itemCollectionHeader;

  @override
  __MyHeaderWidgetState createState() => __MyHeaderWidgetState();
}

class __MyHeaderWidgetState extends State<_MyHeaderWidget> {
  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: () {
        widget.viewAllClicked();
      },
      child: Padding(
        padding: const EdgeInsets.only(
            top: PsDimens.space20,
            left: PsDimens.space16,
            right: PsDimens.space16,
            bottom: PsDimens.space10),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          crossAxisAlignment: CrossAxisAlignment.end,
          children: <Widget>[
            Expanded(
              child: Text(widget.headerName,
                  style: Theme.of(context).textTheme.titleLarge!.copyWith(
                      fontWeight: FontWeight.bold,
                      color: PsColors.textPrimaryDarkColor)),
            ),
            Text(
              Utils.getString('dashboard__view_all'),
              textAlign: TextAlign.start,
              style: Theme.of(context)
                  .textTheme
                  .bodySmall!
                  .copyWith(color: Colors.blue),
            ),
          ],
        ),
      ),
    );
  }
}
