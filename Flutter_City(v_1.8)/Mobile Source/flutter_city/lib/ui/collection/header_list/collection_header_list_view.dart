import 'package:flutter/material.dart';
import 'package:fluttercity/config/ps_config.dart';
import 'package:fluttercity/constant/route_paths.dart';
import 'package:fluttercity/provider/itemcollection/item_collection_provider.dart';
import 'package:fluttercity/repository/item_collection_repository.dart';
import 'package:fluttercity/ui/collection/item/collection_header_list_item.dart';
import 'package:fluttercity/ui/common/ps_admob_banner_widget.dart';
// import 'package:fluttercity/ui/common/ps_admob_banner_widget.dart';
import 'package:fluttercity/ui/common/ps_ui_widget.dart';
import 'package:fluttercity/utils/utils.dart';
import 'package:fluttercity/viewobject/common/ps_value_holder.dart';
import 'package:fluttercity/viewobject/holder/intent_holder/collection_intent_holder.dart';
import 'package:google_mobile_ads/google_mobile_ads.dart';
import 'package:provider/provider.dart';

class CollectionHeaderListView extends StatefulWidget {
  const CollectionHeaderListView({Key? key, required this.animationController})
      : super(key: key);
  final AnimationController animationController;
  @override
  State<StatefulWidget> createState() => _CollectionHeaderListItem();
}

class _CollectionHeaderListItem extends State<CollectionHeaderListView> {
  final ScrollController _scrollController = ScrollController();
  ItemCollectionProvider? _itemCollectionProvider;
  ItemCollectionRepository ?itemCollectionRepository;
  PsValueHolder? psValueHolder;
  dynamic data;

  @override
  void initState() {
    _scrollController.addListener(() {
      if (_scrollController.position.pixels ==
          _scrollController.position.maxScrollExtent) {
        _itemCollectionProvider!.nextItemCollectionList();
      }
    });

    super.initState();
  }

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
    itemCollectionRepository = Provider.of<ItemCollectionRepository>(context);
    psValueHolder = Provider.of<PsValueHolder?>(context);

    if (!isConnectedToInternet && PsConfig.showAdMob) {
      print('loading ads....');
      checkConnection();
    }

    return ChangeNotifierProvider<ItemCollectionProvider>(
      lazy: false,
      create: (BuildContext context) {
        final ItemCollectionProvider provider =
            ItemCollectionProvider(repo: itemCollectionRepository);
        provider.loadItemCollectionList();
        _itemCollectionProvider = provider;
        return _itemCollectionProvider!;
      },
      child: Consumer<ItemCollectionProvider>(builder: (BuildContext context,
          ItemCollectionProvider provider, Widget? child) {
        return Column(
          children: <Widget>[
            const PsAdMobBannerWidget(admobSize: AdSize.banner,),
            // Visibility(
            //   visible: PsConfig.showAdMob &&
            //       isSuccessfullyLoaded &&
            //       isConnectedToInternet,
            //   child: AdmobBanner(
            //     adUnitId: Utils.getBannerAdUnitId(),
            //     adSize: AdmobBannerSize.FULL_BANNER,
            //     listener: (AdmobAdEvent event, Map<String, dynamic> map) {
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
            Expanded(
              child: Stack(
                children: <Widget>[
                  Container(
                    child: RefreshIndicator(
                      child: ListView.builder(
                          shrinkWrap: true,
                          physics: const AlwaysScrollableScrollPhysics(),
                          controller: _scrollController,
                          itemCount: provider.itemCollectionList.data!.length,
                          itemBuilder: (BuildContext context, int index) {
                            if (provider.itemCollectionList.data != null ||
                                provider.itemCollectionList.data!.isEmpty) {
                              final int count =
                                  provider.itemCollectionList.data!.length;
                              return CollectionHeaderListItem(
                                animationController: widget.animationController,
                                animation:
                                    Tween<double>(begin: 0.0, end: 1.0).animate(
                                  CurvedAnimation(
                                    parent: widget.animationController,
                                    curve: Interval((1 / count) * index, 1.0,
                                        curve: Curves.fastOutSlowIn),
                                  ),
                                ),
                                itemCollectionHeader:
                                    provider.itemCollectionList.data![index],
                                onTap: () {
                                  Navigator.pushNamed(context,
                                      RoutePaths.itemListByCollectionId,
                                      arguments: CollectionIntentHolder(
                                        itemCollectionHeader: provider
                                            .itemCollectionList.data![index],
                                        appBarTitle: provider.itemCollectionList
                                            .data![index].name!,
                                      ));
                                },
                              );
                            } else {
                              return Container();
                            }
                          }),
                      onRefresh: () {
                        return provider.resetItemCollectionList();
                      },
                    ),
                  ),
                  PSProgressIndicator(provider.itemCollectionList.status)
                ],
              ),
            )
          ],
        );
      }),
    );
  }
}
