import 'package:flutter/material.dart';
import 'package:fluttercity/config/ps_colors.dart';
import 'package:fluttercity/config/ps_config.dart';
import 'package:fluttercity/constant/ps_constants.dart';
import 'package:fluttercity/provider/category/category_provider.dart';
import 'package:fluttercity/repository/category_repository.dart';
import 'package:fluttercity/ui/common/base/ps_widget_with_appbar.dart';
import 'package:fluttercity/ui/common/ps_admob_banner_widget.dart';
// import 'package:fluttercity/ui/common/ps_admob_banner_widget.dart';
import 'package:fluttercity/utils/utils.dart';
import 'package:fluttercity/viewobject/common/ps_value_holder.dart';
import 'package:fluttercity/viewobject/holder/category_parameter_holder.dart';
import 'package:fluttericon/font_awesome5_icons.dart';
import 'package:google_mobile_ads/google_mobile_ads.dart';
import 'package:provider/provider.dart';

import 'filter_expantion_tile_view.dart';

class FilterListView extends StatefulWidget {
  const FilterListView({this.selectedData});

  final dynamic selectedData;

  @override
  State<StatefulWidget> createState() => _FilterListViewState();
}

class _FilterListViewState extends State<FilterListView> {
  final ScrollController _scrollController = ScrollController();

  final CategoryParameterHolder categoryIconList = CategoryParameterHolder();
  CategoryRepository? categoryRepository;
  PsValueHolder? psValueHolder;

  @override
  void initState() {
    super.initState();
  }

  @override
  void dispose() {
    super.dispose();
  }

  void onSubCategoryClick(Map<String, String> subCategory) {
    Navigator.pop(context, subCategory);
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
    if (!isConnectedToInternet && PsConfig.showAdMob) {
      print('loading ads....');
      checkConnection();
    }
    categoryRepository = Provider.of<CategoryRepository>(context);
    psValueHolder = Provider.of<PsValueHolder?>(context);
    return PsWidgetWithAppBar<CategoryProvider>(
        appBarTitle: Utils.getString( 'search__category'),
        initProvider: () {
          return CategoryProvider(
              repo: categoryRepository, psValueHolder: psValueHolder);
        },
        onProviderReady: (CategoryProvider provider) {
          // provider.loadAllCategoryList(categoryIconList.toMap());
          provider.loadCategoryList(provider.categoryParameterHolder.toMap(),
              Utils.checkUserLoginId(provider.psValueHolder!));
        },
        actions: <Widget>[
          IconButton(
            icon: Icon(FontAwesome5.filter,
                color: PsColors.mainColor),
            onPressed: () {
              final Map<String, String> dataHolder = <String, String>{};
              dataHolder[PsConst.CATEGORY_ID] = '';
              dataHolder[PsConst.SUB_CATEGORY_ID] = '';
              dataHolder[PsConst.CATEGORY_NAME] = '';
              onSubCategoryClick(dataHolder);
            },
          )
        ],
        builder:
            (BuildContext context, CategoryProvider provider, Widget? child) {
          return Container(
            child: SingleChildScrollView(
              child: Column(
                children: <Widget>[
                  const PsAdMobBannerWidget(admobSize: AdSize.banner,),
                  Container(
                    child: ListView.builder(
                        shrinkWrap: true,
                        controller: _scrollController,
                        itemCount: provider.categoryList.data!.length,
                        itemBuilder: (BuildContext context, int index) {
                          if (provider.categoryList.data != null ||
                              provider.categoryList.data!.isEmpty) {
                            return FilterExpantionTileView(
                                selectedData: widget.selectedData,
                                category: provider.categoryList.data![index],
                                onSubCategoryClick: onSubCategoryClick);
                          } else {
                            return Container();
                          }
                        }),
                  )
                ],
              ),
            ),
          );
        });
  }
}
