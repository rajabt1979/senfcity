import 'package:flutter/material.dart';
import 'package:fluttercity/api/common/ps_status.dart';
import 'package:fluttercity/config/ps_colors.dart';
import 'package:fluttercity/config/ps_config.dart';
import 'package:fluttercity/provider/category/category_provider.dart';
import 'package:fluttercity/repository/category_repository.dart';
import 'package:fluttercity/ui/common/base/ps_widget_with_appbar.dart';
import 'package:fluttercity/ui/common/ps_frame_loading_widget.dart';
import 'package:fluttercity/ui/common/ps_ui_widget.dart';
import 'package:fluttercity/utils/utils.dart';
import 'package:fluttercity/viewobject/holder/category_parameter_holder.dart';
import 'package:provider/provider.dart';
import 'package:shimmer/shimmer.dart';

import '../../../viewobject/common/ps_value_holder.dart';
import '../item/category_search_list_item.dart';

class CategoryFilterListView extends StatefulWidget {
  const CategoryFilterListView({required this.categoryName});

  final String? categoryName;
  @override
  State<StatefulWidget> createState() {
    return _CategoryFilterListViewState();
  }
}

class _CategoryFilterListViewState extends State<CategoryFilterListView>
    with TickerProviderStateMixin {
  final ScrollController _scrollController = ScrollController();

  CategoryProvider? _categoryProvider;
  final CategoryParameterHolder categoryIconList = CategoryParameterHolder();
  AnimationController? animationController;
  Animation<double>? animation;

  @override
  void dispose() {
    animationController!.dispose();
    animation = null;
    super.dispose();
  }

  @override
  void initState() {
    _scrollController.addListener(() {
      if (_scrollController.position.pixels ==
          _scrollController.position.maxScrollExtent) {
        // _categoryProvider.nextCategoryList(categoryIconList.toMap());
        _categoryProvider!.nextCategoryList(
            _categoryProvider!.categoryParameterHolder.toMap(),
            Utils.checkUserLoginId(_categoryProvider!.psValueHolder!));
      }
    });

    animationController =
        AnimationController(duration: PsConfig.animation_duration, vsync: this);
    animation = Tween<double>(
      begin: 0.0,
      end: 10.0,
    ).animate(animationController!);
    super.initState();
  }

  CategoryRepository ?repo1;
  String selectedName = 'selectedName';

  @override
  Widget build(BuildContext context) {
    if ( selectedName != '') {
      selectedName = widget.categoryName!;
    }
    Future<bool> _requestPop() {
      animationController!.reverse().then<dynamic>(
        (void data) {
          if (!mounted) {
            return Future<bool>.value(false);
          }
          if (selectedName == '') {
            Navigator.pop(context, true);
          } else {
            Navigator.pop(context, false);
          }
          return Future<bool>.value(true);
        },
      );
      return Future<bool>.value(false);
    }

    repo1 = Provider.of<CategoryRepository>(context);

    print(
        '............................Build UI Again ............................');

    return WillPopScope(
      onWillPop: _requestPop,
      child: PsWidgetWithAppBar<CategoryProvider>(
          appBarTitle:
              Utils.getString('category_search_list__app_bar_name'),
          initProvider: () {
            return CategoryProvider(
                repo: repo1,
                psValueHolder: Provider.of<PsValueHolder?>(context)
                );
          },
          onProviderReady: (CategoryProvider? provider) {
            // provider.loadCategoryList(categoryIconList.toMap());
            provider!.loadCategoryList(provider.categoryParameterHolder.toMap(),
                Utils.checkUserLoginId(provider.psValueHolder!));
            _categoryProvider = provider;
          },
          builder:
              (BuildContext context, CategoryProvider provider, Widget? child) {
            return Stack(children: <Widget>[
              Container(
                  child: RefreshIndicator(
                child: ListView.builder(
                    controller: _scrollController,
                    itemCount: provider.categoryList.data!.length,
                    itemBuilder: (BuildContext context, int index) {
                      if (provider.categoryList.status ==
                          PsStatus.BLOCK_LOADING) {
                        return Shimmer.fromColors(
                            baseColor: PsColors.grey,
                            highlightColor: PsColors.white,
                            child: Column(children: const <Widget>[
                              PsFrameUIForLoading(),
                              PsFrameUIForLoading(),
                              PsFrameUIForLoading(),
                              PsFrameUIForLoading(),
                              PsFrameUIForLoading(),
                              PsFrameUIForLoading(),
                              PsFrameUIForLoading(),
                              PsFrameUIForLoading(),
                              PsFrameUIForLoading(),
                              PsFrameUIForLoading(),
                            ]));
                      } else {
                        final int count = provider.categoryList.data!.length;
                        animationController!.forward();
                        return FadeTransition(
                            opacity: animation!,
                            child: CategoryFilterListItem(
                              selectedName: selectedName,
                              animationController: animationController!,
                              animation:
                                  Tween<double>(begin: 0.0, end: 1.0).animate(
                                CurvedAnimation(
                                  parent: animationController!,
                                  curve: Interval((1 / count) * index, 1.0,
                                      curve: Curves.fastOutSlowIn),
                                ),
                              ),
                              category: provider.categoryList.data![index],
                              onTap: () {
                                print(provider.categoryList.data![index]
                                    .defaultPhoto!.imgPath);
                                Navigator.pop(
                                    context, provider.categoryList.data![index]);
                                print(provider.categoryList.data![index].name);
                              },
                            ));
                      }
                    }),
                onRefresh: () {
                  // return provider.resetCategoryList(categoryIconList.toMap());
                  return provider.resetCategoryList(
                      _categoryProvider!.categoryParameterHolder.toMap(),
                      Utils.checkUserLoginId(_categoryProvider!.psValueHolder!));
                },
              )),
              PSProgressIndicator(provider.categoryList.status)
            ]);
          }),
    );
  }
}
