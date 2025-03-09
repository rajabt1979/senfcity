import 'package:flutter/material.dart';
import 'package:fluttercity/api/common/ps_status.dart';
import 'package:fluttercity/config/ps_colors.dart';
import 'package:fluttercity/config/ps_config.dart';
import 'package:fluttercity/constant/ps_constants.dart';
import 'package:fluttercity/constant/ps_dimens.dart';
import 'package:fluttercity/constant/route_paths.dart';
import 'package:fluttercity/provider/item/item_offset_provider.dart';
import 'package:fluttercity/provider/item/search_item_provider.dart';
import 'package:fluttercity/repository/item_repository.dart';
import 'package:fluttercity/ui/common/ps_admob_banner_widget.dart';
import 'package:fluttercity/ui/common/ps_ui_widget.dart';
import 'package:fluttercity/ui/items/item/item_vertical_list_item.dart';
import 'package:fluttercity/utils/utils.dart';
import 'package:fluttercity/viewobject/common/ps_value_holder.dart';
import 'package:fluttercity/viewobject/holder/intent_holder/item_detail_intent_holder.dart';
import 'package:fluttercity/viewobject/holder/item_parameter_holder.dart';
import 'package:fluttercity/viewobject/item.dart';
import 'package:fluttericon/font_awesome_icons.dart';
import 'package:google_mobile_ads/google_mobile_ads.dart';
import 'package:provider/provider.dart';

class ItemListWithFilterView extends StatefulWidget {
  const ItemListWithFilterView(
      {Key ?key,
        required this.itemParameterHolder,
        required this.animationController,
        this.changeAppBarTitle})
      : super(key: key);

  final ItemParameterHolder itemParameterHolder;
  final AnimationController animationController;
  final Function? changeAppBarTitle;

  @override
  _ItemListWithFilterViewState createState() => _ItemListWithFilterViewState();
}

class _ItemListWithFilterViewState extends State<ItemListWithFilterView>
    with TickerProviderStateMixin {
  final ScrollController _scrollController = ScrollController();

  SearchItemProvider? _searchItemProvider;
  // ItemOffsetProvider? _itemOffsetProvider;
  bool isVisible = true;
  @override
  void dispose() {
    super.dispose();
  }

  @override
  void initState() {
    super.initState();

    _offset = 0;
    _scrollController.addListener(() {
      if (_scrollController.position.pixels ==
          _scrollController.position.maxScrollExtent) {
        _searchItemProvider!
            .nextItemListByKey(_searchItemProvider!.itemParameterHolder);
      }
      setState(() {
        final double offset = _scrollController.offset;
        _delta = offset - _oldOffset!;
        if (_delta! > _containerMaxHeight)
          _delta = _containerMaxHeight;
        else if (_delta! < 0) {
          _delta = 0;
        }
        _oldOffset = offset;
        _offset = -_delta!;
      });

      print(' Offset $_offset');
    });
  }

  final double _containerMaxHeight = 60;
  double? _offset, _delta = 0, _oldOffset = 0;
  ItemRepository? itemRepo;
  dynamic data;
  PsValueHolder? valueHolder;
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
    itemRepo = Provider.of<ItemRepository>(context);
    //_itemOffsetProvider = Provider.of(context, listen: false);
    valueHolder = Provider.of<PsValueHolder?>(context);

    if (!isConnectedToInternet && PsConfig.showAdMob) {
      print('loading ads....');
      checkConnection();
    }
    print(
        '............................Build Item List UI Again ............................');
    return ChangeNotifierProvider<SearchItemProvider>(
        lazy: false,
        create: (BuildContext context) {
          final SearchItemProvider provider =
          SearchItemProvider(repo: itemRepo, psValueHolder: valueHolder);
          provider.loadItemListByKey(widget.itemParameterHolder);
          _searchItemProvider = provider;
          _searchItemProvider!.itemParameterHolder = widget.itemParameterHolder;
          return _searchItemProvider!;
        },
        child: Consumer<SearchItemProvider>(builder:
            (BuildContext context, SearchItemProvider provider, Widget? child) {
          return Column(
            children: <Widget>[
              const PsAdMobBannerWidget(admobSize: AdSize.banner,),
              Expanded(
                child: Container(
                  color: PsColors.coreBackgroundColor,
                  child: Stack(children: <Widget>[
                    if (provider.itemList.data!.isNotEmpty &&
                        provider.itemList.data != null)
                      Container(
                          color: PsColors.coreBackgroundColor,
                          margin: const EdgeInsets.only(
                              left: PsDimens.space8,
                              right: PsDimens.space8,
                              top: PsDimens.space4,
                              bottom: PsDimens.space4),
                          child: RefreshIndicator(
                            child: CustomScrollView(
                                controller: _scrollController,
                                scrollDirection: Axis.vertical,
                                shrinkWrap: true,
                                slivers: <Widget>[
                                  SliverGrid(
                                    gridDelegate:
                                    const SliverGridDelegateWithMaxCrossAxisExtent(
                                        maxCrossAxisExtent: 220,
                                        childAspectRatio: 0.6),
                                    delegate: SliverChildBuilderDelegate(
                                          (BuildContext context, int index) {
                                        if (provider.itemList.data != null ||
                                            provider.itemList.data!.isNotEmpty) {
                                          final List<Item> dataList =
                                          provider.itemList.data!;
                                          final int count =
                                              provider.itemList.data!.length;
                                          return ItemVeticalListItem(
                                            coreTagKey:
                                            provider.hashCode.toString() +
                                                provider.itemList
                                                    .data![index].id!,
                                            animationController:
                                            widget.animationController,
                                            animation: Tween<double>(
                                                begin: 0.0, end: 1.0)
                                                .animate(
                                              CurvedAnimation(
                                                parent:
                                                widget.animationController,
                                                curve: Interval(
                                                    (1 / count) * index, 1.0,
                                                    curve:
                                                    Curves.fastOutSlowIn),
                                              ),
                                            ),
                                            item: dataList[index],
                                            onTap: () async {
                                              final Item item = dataList[index];
                                              final ItemDetailIntentHolder
                                              holder =
                                              ItemDetailIntentHolder(
                                                itemId: item.id,
                                                heroTagImage: provider.hashCode
                                                    .toString() +
                                                    item.id! +
                                                    PsConst.HERO_TAG__IMAGE,
                                                heroTagTitle: provider.hashCode
                                                    .toString() +
                                                    item.id! +
                                                    PsConst.HERO_TAG__TITLE,
                                                heroTagOriginalPrice: provider
                                                    .hashCode
                                                    .toString() +
                                                    item.id! +
                                                    PsConst
                                                        .HERO_TAG__ORIGINAL_PRICE,
                                                heroTagUnitPrice: provider
                                                    .hashCode
                                                    .toString() +
                                                    item.id! +
                                                    PsConst
                                                        .HERO_TAG__UNIT_PRICE,
                                              );

                                              //final dynamic result =
                                              await Navigator.pushNamed(context,
                                                  RoutePaths.itemDetail,
                                                  arguments: holder);

                                              // if (result == null) {
                                              //   provider.loadItemListByKey(
                                              //       widget.itemParameterHolder);
                                              // }
                                            },
                                          );
                                        } else {
                                          return null;
                                        }
                                      },
                                      childCount: provider.itemList.data!.length,
                                    ),
                                  ),
                                ]),
                            onRefresh: () {
                              return provider.resetLatestItemList(
                                  _searchItemProvider!.itemParameterHolder);
                            },
                          ))
                    else if (provider.itemList.status !=
                        PsStatus.PROGRESS_LOADING &&
                        provider.itemList.status != PsStatus.BLOCK_LOADING &&
                        provider.itemList.status != PsStatus.NOACTION
                    )
                      Align(
                        child: Container(
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            crossAxisAlignment: CrossAxisAlignment.center,
                            mainAxisSize: MainAxisSize.min,
                            children: <Widget>[
                              Image.asset(
                                'assets/images/baseline_empty_item_grey_24.png',
                                height: 100,
                                width: 150,
                                fit: BoxFit.contain,
                              ),
                              const SizedBox(
                                height: PsDimens.space32,
                              ),
                              Padding(
                                padding: const EdgeInsets.only(
                                    left: PsDimens.space20,
                                    right: PsDimens.space20),
                                child: Text(
                                  Utils.getString(
                                      'procuct_list__no_result_data'),
                                  textAlign: TextAlign.center,
                                  style: Theme.of(context)
                                      .textTheme
                                      .titleLarge!
                                      .copyWith(),
                                ),
                              ),
                              const SizedBox(
                                height: PsDimens.space20,
                              ),
                            ],
                          ),
                        ),
                      ),
                    Consumer<ItemOffsetProvider>(
                      builder: (BuildContext context,
                          ItemOffsetProvider itemOffsetProvider, _) =>
                          Positioned(
                            bottom: itemOffsetProvider.containerOffset,
                            width: MediaQuery.of(context).size.width,
                            child: Container(
                              margin: const EdgeInsets.only(
                                  left: PsDimens.space12,
                                  top: PsDimens.space8,
                                  right: PsDimens.space12,
                                  bottom: PsDimens.space16),
                              child: Container(
                                  width: double.infinity,
                                  height: _containerMaxHeight,
                                  child: BottomNavigationImageAndText(
                                      changeAppBarTitle: widget.changeAppBarTitle,
                                      searchItemProvider: _searchItemProvider)),
                            ),
                          ),
                    ),
                    PSProgressIndicator(provider.itemList.status),
                  ]),
                ),
              )
            ],
          );
        }));
  }
}

class BottomNavigationImageAndText extends StatelessWidget {
  const BottomNavigationImageAndText({
    this.searchItemProvider,
    this.changeAppBarTitle,
  });
  final SearchItemProvider? searchItemProvider;
  final Function? changeAppBarTitle;
  @override
  Widget build(BuildContext context) {
    bool isFiltered = searchItemProvider!.itemParameterHolder.isFiltered();
    final String catId = searchItemProvider!.itemParameterHolder.catId!;
    final String subCatid = searchItemProvider!.itemParameterHolder.subCatId!;

    return Container(
      decoration: BoxDecoration(
          border: Border.all(color: PsColors.mainLightShadowColor),
          boxShadow: <BoxShadow>[
            BoxShadow(
                color: PsColors.mainShadowColor,
                offset: const Offset(1.1, 1.1),
                blurRadius: 10.0),
          ],
          color: PsColors.backgroundColor,
          borderRadius:
          const BorderRadius.all(Radius.circular(PsDimens.space8))),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
        children: <Widget>[
          BottomNavigationItem(
            textColor:
            isFiltered ? PsColors.mainColor : PsColors.textPrimaryColor,
            iconColor: isFiltered ? PsColors.mainColor : PsColors.iconColor,
            icon: FontAwesome.list_bullet,
            title: Utils.getString('search__category'),
            onTap: () async {
              final Map<String, String> dataHolder = <String, String>{
                PsConst.CATEGORY_ID: catId,
                PsConst.SUB_CATEGORY_ID: subCatid,
              };

              final dynamic result = await Navigator.pushNamed(
                  context, RoutePaths.filterExpantion,
                  arguments: dataHolder);

              if (result != null) {
                searchItemProvider!.itemParameterHolder.catId =
                result[PsConst.CATEGORY_ID];
                searchItemProvider!.itemParameterHolder.subCatId =
                result[PsConst.SUB_CATEGORY_ID];

                searchItemProvider!.resetLatestItemList(
                    searchItemProvider!.itemParameterHolder);

                // if (result[PsConst.CATEGORY_ID] == '' &&
                //     result[PsConst.SUB_CATEGORY_ID] == '') {

                // } else {
                //   changeAppBarTitle(result[PsConst.CATEGORY_NAME]);
                // }
              }
            },
          ),
          BottomNavigationItem(
            textColor:
            isFiltered ? PsColors.mainColor : PsColors.textPrimaryColor,
            iconColor: isFiltered ? PsColors.mainColor : PsColors.iconColor,
            icon: Icons.filter_list,
            title: Utils.getString('search__filter'),
            onTap: () async {
              final dynamic result = await Navigator.pushNamed(
                  context, RoutePaths.itemSearch,
                  arguments: searchItemProvider!.itemParameterHolder);
              if (result != null) {
                searchItemProvider!.itemParameterHolder = result;
                searchItemProvider!.resetLatestItemList(
                    searchItemProvider!.itemParameterHolder);

                if (searchItemProvider!.itemParameterHolder
                    .isFiltered()) {
                  isFiltered = true;
                } else {
                  isFiltered = false;
                }
              }
            },
          ),
          BottomNavigationItem(
            textColor:
            isFiltered ? PsColors.mainColor : PsColors.textPrimaryColor,
            iconColor: PsColors.mainColor,
            icon: Icons.sort,
            title: Utils.getString('search__map_filter'),
            onTap: () async {
              if (PsConfig.isUseGoogleMap) {
                final dynamic result = await Navigator.pushNamed(
                    context, RoutePaths.googleMapFilter,
                    arguments: searchItemProvider!.itemParameterHolder);
                if (result != null && result is ItemParameterHolder) {
                  searchItemProvider!.itemParameterHolder = result;
                  if (searchItemProvider!.itemParameterHolder.miles != null &&
                      searchItemProvider!.itemParameterHolder.miles != '' &&
                      double.parse(
                          searchItemProvider!.itemParameterHolder.miles!) <
                          1) {
                    searchItemProvider!.itemParameterHolder.miles = '1';
                  } //for 0.5 km, it is less than 1 miles and error
                  searchItemProvider!.resetLatestItemList(
                      searchItemProvider!.itemParameterHolder);
                }
              } else {
                final dynamic result = await Navigator.pushNamed(
                    context, RoutePaths.mapFilter,
                    arguments: searchItemProvider!.itemParameterHolder);

                if (result != null && result is ItemParameterHolder) {
                  searchItemProvider!.itemParameterHolder = result;
                  if (searchItemProvider!.itemParameterHolder.miles != null &&
                      searchItemProvider!.itemParameterHolder.miles != '' &&
                      double.parse(
                          searchItemProvider!.itemParameterHolder.miles!) <
                          1) {
                    searchItemProvider!.itemParameterHolder.miles = '1';
                  } //for 0.5 km, it is less than 1 miles and error
                  searchItemProvider!.resetLatestItemList(
                      searchItemProvider!.itemParameterHolder);
                }
              }
            },
          ),
        ],
      ),
    );
  }
}

class BottomNavigationItem extends StatelessWidget {
  const BottomNavigationItem({
    required this.iconColor,
    required this.icon,
    required this.onTap,
    required this.title,
    required this.textColor,
  });
  final IconData icon;
  final Color? iconColor;
  final Function onTap;
  final String title;
  final Color textColor;

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: <Widget>[
          Icon(icon, color: iconColor ?? PsColors.grey),
          Text(
            title,
            style: Theme.of(context)
                .textTheme
                .bodyLarge!
                .copyWith(color: textColor),
          ),
        ],
      ),
      onTap: onTap as void Function()?,
    );
  }
}
