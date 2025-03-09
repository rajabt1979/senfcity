import 'package:flutter/material.dart';
import 'package:fluttercity/config/ps_colors.dart';
import 'package:fluttercity/config/ps_config.dart';
import 'package:fluttercity/constant/ps_constants.dart';
import 'package:fluttercity/constant/ps_dimens.dart';
import 'package:fluttercity/constant/route_paths.dart';
import 'package:fluttercity/provider/item/search_item_provider.dart';
import 'package:fluttercity/repository/item_repository.dart';
import 'package:fluttercity/ui/common/ps_admob_banner_widget.dart';
import 'package:fluttercity/ui/common/ps_advance_filtering_widget.dart';
import 'package:fluttercity/ui/common/ps_button_widget.dart';
import 'package:fluttercity/ui/common/ps_textfield_widget.dart';
import 'package:fluttercity/utils/utils.dart';
import 'package:fluttercity/viewobject/holder/intent_holder/item_list_intent_holder.dart';
import 'package:fluttercity/viewobject/holder/item_parameter_holder.dart';
import 'package:fluttericon/font_awesome5_icons.dart';
import 'package:google_mobile_ads/google_mobile_ads.dart';
import 'package:provider/provider.dart';

class HomeItemSearchView extends StatefulWidget {
  const HomeItemSearchView({
    required this.itemParameterHolder,
    required this.animation,
    required this.animationController,
  });

  final ItemParameterHolder itemParameterHolder;
  final AnimationController animationController;
  final Animation<double> animation;

  @override
  _ItemSearchViewState createState() => _ItemSearchViewState();
}

class _ItemSearchViewState extends State<HomeItemSearchView> {
  ItemRepository? repo1;
  SearchItemProvider? _searchItemProvider;

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
    print(
        '............................Build UI In Home Item Again ............................');

    final Widget _searchButtonWidget = PSButtonWidget(
        hasShadow: true,
        width: double.infinity,
        titleText: Utils.getString('home_search__search'),
        onPressed: () async {
          //* Solved
          // if (userInputItemNameTextEditingController.text != null &&
          //     userInputItemNameTextEditingController.text != '') {
          //   _searchItemProvider.itemParameterHolder.keyword =
          //       userInputItemNameTextEditingController.text;
          // } else {
          //   _searchItemProvider.itemParameterHolder.keyword = '';
          // }

          //! Unused Code
          // if (_searchItemProvider.categoryId != null) {
          //   _searchItemProvider.itemParameterHolder.catId =
          //       _searchItemProvider.categoryId;
          // }
          // if (_searchItemProvider.subCategoryId != null) {
          //   _searchItemProvider.itemParameterHolder.subCatId =
          //       _searchItemProvider.subCategoryId;
          // }
          final dynamic result =
              await Navigator.pushNamed(context, RoutePaths.filterItemList,
                  arguments: ItemListIntentHolder(
                    appBarTitle: Utils.getString('home_search__app_bar_title'),
                    itemParameterHolder:
                        _searchItemProvider!.itemParameterHolder,
                  ));
          if (result != null && result is ItemParameterHolder) {
            _searchItemProvider!.itemParameterHolder = result;
          }
        });

    repo1 = Provider.of<ItemRepository>(context);
    return SliverToBoxAdapter(
        child: ChangeNotifierProvider<SearchItemProvider>(
            lazy: false,
            create: (BuildContext content) {
              _searchItemProvider = SearchItemProvider(repo: repo1);
              _searchItemProvider!.itemParameterHolder =
                  widget.itemParameterHolder;
              _searchItemProvider!
                  .loadItemListByKey(_searchItemProvider!.itemParameterHolder);
              return _searchItemProvider!;
            },
            child: Consumer<SearchItemProvider>(
              builder: (BuildContext context, SearchItemProvider provider,
                  Widget? child) {
                if (
                //_searchItemProvider!.itemList != null &&
                _searchItemProvider!.itemList.data != null) {
                  widget.animationController.forward();
                  return SingleChildScrollView(
                    child: AnimatedBuilder(
                        animation: widget.animationController,
                        child: Container(
                          color: PsColors.baseColor,
                          child: Column(
                            children: <Widget>[
                              const PsAdMobBannerWidget(admobSize: AdSize.banner,),
                              _SortingWidget(
                                searchItemProvider: provider,
                              ),
                              _ItemNameWidget(
                                userInputItemNameTextEditingController:
                                provider.itemNameController!,
                              ),
                              _RatingRangeWidget(),
                              _SpecialCheckWidget(),
                              Container(
                                  margin: const EdgeInsets.only(
                                      left: PsDimens.space16,
                                      top: PsDimens.space16,
                                      right: PsDimens.space16,
                                      bottom: PsDimens.space40),
                                  child: _searchButtonWidget),
                            ],
                          ),
                        ),
                        builder: (BuildContext context, Widget? child) {
                          return FadeTransition(
                              opacity: widget.animation,
                              child: Transform(
                                transform: Matrix4.translationValues(0.0,
                                    100 * (1.0 - widget.animation.value), 0.0),
                                child: child,
                              ));
                        }),
                  );
                } else {
                  return Container();
                }
              },
            )));
  }
}

class _ItemNameWidget extends StatelessWidget {
  const _ItemNameWidget(
      {required this.userInputItemNameTextEditingController});
  final TextEditingController userInputItemNameTextEditingController;

  @override
  Widget build(BuildContext context) {
    return Column(
      children: <Widget>[
        PsTextFieldWidget(
            titleText: Utils.getString('home_search__set_item_name'),
            hintText: Utils.getString('home_search__not_set'),
            textEditingController: userInputItemNameTextEditingController),
      ],
    );
  }
}

class _ChangeRatingColor extends StatelessWidget {
  const _ChangeRatingColor({
    Key? key,
    required this.title,
    required this.checkColor,
    required this.onTap,
  }) : super(key: key);

  final String title;
  final bool checkColor;
  final Function? onTap;

  @override
  Widget build(BuildContext context) {
    final Color defaultBackgroundColor = PsColors.backgroundColor;
    return InkWell(
      onTap: onTap as void Function()?,
      child: Container(
        width: MediaQuery.of(context).size.width / 5.5,
        height: PsDimens.space104,
        decoration: BoxDecoration(
          color: checkColor ? defaultBackgroundColor : PsColors.mainColor,
        ),
        child: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            crossAxisAlignment: CrossAxisAlignment.center,
            mainAxisSize: MainAxisSize.max,
            children: <Widget>[
              Icon(
                Icons.star,
                color: checkColor ? PsColors.iconColor : PsColors.white,
              ),
              Text(
                title,
                textAlign: TextAlign.center,
                style: Theme.of(context).textTheme.bodySmall!.copyWith(
                      color: checkColor ? PsColors.iconColor : PsColors.white,
                    ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _RatingRangeWidget extends StatefulWidget {
  @override
  __RatingRangeWidgetState createState() => __RatingRangeWidgetState();
}

class __RatingRangeWidgetState extends State<_RatingRangeWidget> {
  @override
  Widget build(BuildContext context) {
    final String ratingText1 = Utils.getString('home_search__one_and_higher');
    final String ratingText2 = Utils.getString('home_search__two_and_higher');
    final String ratingText3 = Utils.getString('home_search__three_and_higher');
    final String ratingText4 = Utils.getString('home_search__four_and_higher');
    final String ratingText5 = Utils.getString('home_search__five');

    return Consumer<SearchItemProvider>(
      builder: (BuildContext context, SearchItemProvider provider, _) => Column(
        mainAxisSize: MainAxisSize.max,
        children: <Widget>[
          Container(
            margin: const EdgeInsets.all(PsDimens.space12),
            child: Row(
              children: <Widget>[
                Text(Utils.getString('home_search__rating_range'),
                    style: Theme.of(context).textTheme.bodyLarge),
              ],
            ),
          ),
          Row(
            mainAxisAlignment: MainAxisAlignment.center,
            mainAxisSize: MainAxisSize.max,
            children: <Widget>[
              _ChangeRatingColor(
                  title: ratingText1,
                  onTap: () => provider.setRatingTo1AndHigher(),
                  checkColor: !provider.isfirstRatingClicked),
              _ChangeRatingColor(
                  title: ratingText2,
                  onTap: () => provider.setRatingTo2AndHigher(),
                  checkColor: !provider.isSecondRatingClicked),
              _ChangeRatingColor(
                  title: ratingText3,
                  onTap: () => provider.setRatingTo3AndHigher(),
                  checkColor: !provider.isThirdRatingClicked),
              _ChangeRatingColor(
                  title: ratingText4,
                  onTap: () => provider.setRatingTo4AndHigher(),
                  checkColor: !provider.isfouthRatingClicked),
              _ChangeRatingColor(
                  title: ratingText5,
                  onTap: () => provider.setRatingTo5Only(),
                  checkColor: !provider.isFifthRatingClicked),
            ],
          ),
        ],
      ),
    );
  }
}

class _SortingWidget extends StatefulWidget {
  const _SortingWidget({required this.searchItemProvider});
  final SearchItemProvider searchItemProvider;

  @override
  __SortingWidgetState createState() => __SortingWidgetState();
}

class __SortingWidgetState extends State<_SortingWidget> {
  @override
  Widget build(BuildContext context) {
    return Column(
      children: <Widget>[
        Container(
          width: double.infinity,
          margin: const EdgeInsets.all(PsDimens.space12),
          child: Text(Utils.getString('home_search__sorting'),
              style: Theme.of(context).textTheme.titleLarge),
        ),
        //! Removed Gesture and Wrapped to SortView
        SortingView(
            onTap: () {
              print('sort by latest item');
              widget.searchItemProvider.sortByLatest();
            },
            image: 'assets/images/baesline_access_time_black_24.png',
            titleText: Utils.getString('item_filter__latest'),
            checkImage: widget.searchItemProvider.itemParameterHolder.orderBy ==
                PsConst.FILTERING__ADDED_DATE
                ? 'assets/images/baseline_check_green_24.png'
                : 'assets/images/baseline_check_green_25.png'),
        SortingView(
            onTap: () {
              print('sort by popular item');
              widget.searchItemProvider.sortByPopular();
            },
            image: 'assets/images/baseline_graph_black_24.png',
            titleText: Utils.getString('item_filter__popular'),
            checkImage: widget.searchItemProvider.itemParameterHolder.orderBy ==
                PsConst.FILTERING__TRENDING
                ? 'assets/images/baseline_check_green_24.png'
                : 'assets/images/baseline_check_green_25.png'),
        SortingView(
            onTap: () {
              print('sort by A to Z');
              widget.searchItemProvider.sortByAtoZ();
            },
            image: 'assets/images/baseline_price_down_black_24.png',
            titleText: Utils.getString('item_filter__atoz'),
            checkImage: widget.searchItemProvider.itemParameterHolder.orderBy ==
                '' &&
                widget.searchItemProvider.itemParameterHolder.orderType ==
                    PsConst.FILTERING__ASC
                ? 'assets/images/baseline_check_green_24.png'
                : 'assets/images/baseline_check_green_25.png'),
        SortingView(
            onTap: () {
              print('sort by Z to A ');
              widget.searchItemProvider.sortByZtoA();
            },
            image: 'assets/images/baseline_price_up_black_24.png',
            titleText: Utils.getString('item_filter__ztoa'),
            checkImage: widget.searchItemProvider.itemParameterHolder.orderBy ==
                '' &&
                widget.searchItemProvider.itemParameterHolder.orderType ==
                    PsConst.FILTERING__DESC
                ? 'assets/images/baseline_check_green_24.png'
                : 'assets/images/baseline_check_green_25.png'),
        const Divider(
          height: PsDimens.space1,
        ),
        const SizedBox(
          height: PsDimens.space8,
        ),
        // const PsAdMobBannerWidget(
        //   admobBannerSize: AdmobBannerSize.MEDIUM_RECTANGLE,
        // ),
      ],
    );
  }
}

class SortingView extends StatefulWidget {
  const SortingView({
    required this.image,
    required this.titleText,
    required this.checkImage,
    required this.onTap,
  });

  final String titleText;
  final String image;
  final String checkImage;
  final Function? onTap;

  @override
  State<StatefulWidget> createState() => _SortingViewState();
}

class _SortingViewState extends State<SortingView> {
  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: widget.onTap as void Function()?,
      child: Container(
        color: PsColors.backgroundColor,
        width: MediaQuery.of(context).size.width,
        height: PsDimens.space60,
        margin: const EdgeInsets.only(top: PsDimens.space4),
        child: Row(
          mainAxisSize: MainAxisSize.max,
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: <Widget>[
            Row(
              mainAxisSize: MainAxisSize.max,
              mainAxisAlignment: MainAxisAlignment.spaceAround,
              children: <Widget>[
                Padding(
                  padding: const EdgeInsets.all(PsDimens.space16),
                  child: Image.asset(
                    widget.image,
                    color: Theme.of(context).iconTheme.color,
                    width: PsDimens.space24,
                    height: PsDimens.space24,
                  ),
                ),
                const SizedBox(
                  width: PsDimens.space10,
                ),
                Text(widget.titleText,
                    style: Theme.of(context).textTheme.bodyLarge),
              ],
            ),
            Padding(
                padding: const EdgeInsets.only(
                    right: PsDimens.space20, left: PsDimens.space20),
                child: widget.checkImage != ''
                    ? Image.asset(
                        widget.checkImage,
                        width: PsDimens.space20,
                        height: PsDimens.space20,
                      )
                    : const SizedBox()),
          ],
        ),
      ),
    );
  }
}

class _SpecialCheckWidget extends StatefulWidget {
  @override
  __SpecialCheckWidgetState createState() => __SpecialCheckWidgetState();
}

class __SpecialCheckWidgetState extends State<_SpecialCheckWidget> {
  @override
  Widget build(BuildContext context) {
    return Column(
      children: <Widget>[
        Container(
          margin: const EdgeInsets.all(PsDimens.space12),
          child: Row(
            children: <Widget>[
              Text(Utils.getString('home_search__advance_filtering'),
                  style: Theme.of(context).textTheme.bodyLarge),
            ],
          ),
        ),
        AdvanceFilteringWidget(
            title: Utils.getString('home_search__featured_item'),
            icon: FontAwesome5.gem,
            checkTitle: 1,
            size: PsDimens.space18),
        const Divider(
          height: PsDimens.space1,
        ),
        AdvanceFilteringWidget(
            title: Utils.getString('home_search__discount_item'),
            icon: FontAwesome5.percent,
            checkTitle: 2,
            size: PsDimens.space18),
        const Divider(
          height: PsDimens.space1,
        ),
      ],
    );
  }
}

//! UnUsed Code //
// class _AdvanceFilteringWidget extends StatefulWidget {
//   const _AdvanceFilteringWidget({
//     Key key,
//     @required this.title,
//     @required this.icon,
//     @required this.checkTitle,
//   }) : super(key: key);

//   final String title;
//   final IconData icon;
//   final bool checkTitle;

//   @override
//   __AdvanceFilteringWidgetState createState() =>
//       __AdvanceFilteringWidgetState();
// }

// class __AdvanceFilteringWidgetState extends State<_AdvanceFilteringWidget> {
//   @override
//   Widget build(BuildContext context) {
//     return Consumer<SearchItemProvider>(
//       builder: (BuildContext context, SearchItemProvider provider, _) =>
//           Container(
//               width: double.infinity,
//               height: PsDimens.space52,
//               child: Container(
//                 margin: const EdgeInsets.all(PsDimens.space12),
//                 child: Row(
//                   mainAxisAlignment: MainAxisAlignment.spaceBetween,
//                   children: <Widget>[
//                     Row(
//                       children: <Widget>[
//                         Icon(
//                           widget.icon,
//                           size: PsDimens.space20,
//                         ),
//                         const SizedBox(
//                           width: PsDimens.space10,
//                         ),
//                         Text(
//                           widget.title,
//                           style: Theme.of(context)
//                               .textTheme
//                               .bodyMedium
//                               .copyWith(fontWeight: FontWeight.normal),
//                         ),
//                       ],
//                     ),
//                     if (widget.checkTitle)
//                       Switch(
//                         value: provider.isSwitchedFeaturedItem,
//                         onChanged: (bool value) {
//                           provider.switchToFeaturedItem(value);
//                         },
//                         activeTrackColor: PsColors.mainColor,
//                         activeColor: PsColors.mainColor,
//                       )
//                     else
//                       Switch(
//                         value: provider.isSwitchedDiscountPrice,
//                         onChanged: (bool value) {
//                           provider.switchToDiscountItem(value);
//                         },
//                         activeTrackColor: PsColors.mainColor,
//                         activeColor: PsColors.mainColor,
//                       ),
//                   ],
//                 ),
//               )),
//     );
//   }
// }
