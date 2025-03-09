import 'package:flutter/material.dart';
import 'package:fluttercity/api/common/ps_resource.dart';
import 'package:fluttercity/config/ps_colors.dart';
import 'package:fluttercity/config/ps_config.dart';
import 'package:fluttercity/constant/ps_dimens.dart';
import 'package:fluttercity/constant/route_paths.dart';
import 'package:fluttercity/provider/user/user_provider.dart';
import 'package:fluttercity/repository/user_repository.dart';
import 'package:fluttercity/ui/common/dialog/confirm_dialog_view.dart';
import 'package:fluttercity/ui/common/dialog/error_dialog.dart';
import 'package:fluttercity/ui/common/ps_admob_banner_widget.dart';
import 'package:fluttercity/utils/ps_progress_dialog.dart';
import 'package:fluttercity/utils/utils.dart';
import 'package:fluttercity/viewobject/api_status.dart';
import 'package:fluttercity/viewobject/common/ps_value_holder.dart';
import 'package:fluttercity/viewobject/holder/delete_user_holder.dart';
import 'package:fluttercity/viewobject/holder/intent_holder/user_item_list_intent_holder.dart';
import 'package:google_mobile_ads/google_mobile_ads.dart';
import 'package:provider/provider.dart';

class MoreView extends StatefulWidget {
  const MoreView(
      {Key? key,
      required this.closeMoreContainerView,
      required this.animationController})
      : super(key: key);

  final AnimationController animationController;
    final Function closeMoreContainerView;

  @override
  _MoreViewState createState() => _MoreViewState();
}

class _MoreViewState extends State<MoreView> {
  bool isConnectedToInternet = false;
  bool isSuccessfullyLoaded = true;
  PsValueHolder? valueHolder;
  UserProvider? userProvider;
  UserRepository? userRepository;
  String? shopId;
  String? shopName;

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
    userRepository = Provider.of<UserRepository>(context);
    valueHolder = Provider.of<PsValueHolder?>(context);

    if (!isConnectedToInternet && PsConfig.showAdMob) {
      print('loading ads....');
      checkConnection();
    }
    final Animation<double> animation = Tween<double>(begin: 0.0, end: 1.0)
        .animate(CurvedAnimation(
            parent: widget.animationController,
            curve: const Interval(0.5 * 1, 1.0, curve: Curves.fastOutSlowIn)));

    widget.animationController.forward();

    return ChangeNotifierProvider<UserProvider>(
        lazy: false,
        create: (BuildContext context) {
          userProvider =
              UserProvider(repo: userRepository, psValueHolder: valueHolder);
          return userProvider!;
        },
        child: Consumer<UserProvider>(builder:
            (BuildContext context, UserProvider userProvider, Widget? child) {
          return AnimatedBuilder(
            animation: widget.animationController,
            child: SingleChildScrollView(
              child: Column(
                mainAxisSize: MainAxisSize.max,
                children: <Widget>[
                  const SizedBox(height: PsDimens.space4),
                  _MorePostTitleWidget(),
                  const SizedBox(height: PsDimens.space4),
                  _MorePendingPostWidget(
                    animationController: widget.animationController,
                    userId: userProvider.psValueHolder!.loginUserId!,
                    animation: Tween<double>(begin: 0.0, end: 1.0).animate(
                      CurvedAnimation(
                        parent: widget.animationController,
                        curve: const Interval((1 / 4) * 2, 1.0,
                            curve: Curves.fastOutSlowIn),
                      ),
                    ),
                    headerTitle: Utils.getString('more__pending_post_title'),
                    status: '0',
                  ),
                  const SizedBox(height: PsDimens.space4),
                  _MoreActivePostWidget(
                    animationController: widget.animationController,
                    userId: userProvider.psValueHolder!.loginUserId!,
                    animation: Tween<double>(begin: 0.0, end: 1.0).animate(
                      CurvedAnimation(
                        parent: widget.animationController,
                        curve: const Interval((1 / 4) * 2, 1.0,
                            curve: Curves.fastOutSlowIn),
                      ),
                    ),
                    headerTitle: Utils.getString('more__active_post_title'),
                    status: '1', //animatio
                  ),
                  const SizedBox(height: PsDimens.space4),
                  _MoreFavouriteWidget(),
                  const SizedBox(height: PsDimens.space4),
                  _MoreActivityTitleWidget(),
                  const SizedBox(height: PsDimens.space4),
                  _MorePaidAdsWidget(),
                  const SizedBox(height: PsDimens.space4),
                  _MoreUploadedItemWidget(),
                  const SizedBox(height: PsDimens.space4),
                  _MoreHistoryteWidget(),
                  const SizedBox(height: PsDimens.space4),
                  _MoreSettingAndPrivacyTitleWidget(),
                  const SizedBox(height: PsDimens.space4),
                  _MoreDeactivateWidget(
                    userProvider: userProvider,
                    closeMoreContainerView: widget.closeMoreContainerView,
                  ),
                  const SizedBox(height: PsDimens.space4),
                  _MoreSettingWidget(),
                  const SizedBox(height: PsDimens.space4),
                  const PsAdMobBannerWidget(
                    admobSize: AdSize.mediumRectangle,
                    // admobBannerSize: AdmobBannerSize.MEDIUM_RECTANGLE,
                  ),
                ],
              ),
            ),
            builder: (BuildContext context, Widget ?child) {
              return FadeTransition(
                opacity: animation,
                child: Transform(
                    transform: Matrix4.translationValues(
                        0.0, 100 * (1.0 - animation.value), 0.0),
                    child: child),
              );
            },
          );
        }));
  }
}

class _MoreDeactivateWidget extends StatelessWidget {
  const _MoreDeactivateWidget(
      {required this.userProvider, required this.closeMoreContainerView});

  final UserProvider userProvider;
  final Function closeMoreContainerView;

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: () {
        showDialog<dynamic>(
            context: context,
            builder: (BuildContext context) {
              return ConfirmDialogView(
                  description: Utils.getString(
                      'profile__deactivate_confirm_text'),
                  leftButtonText: Utils.getString('dialog__cancel'),
                  rightButtonText: Utils.getString( 'dialog__ok'),
                  onAgreeTap: () async {
                       Navigator.of(context).pop();
                    await PsProgressDialog.showDialog(context);
                    final DeleteUserHolder deleteUserHolder = DeleteUserHolder(
                        userId: userProvider.psValueHolder!.loginUserId);
                    final PsResource<ApiStatus> _apiStatus = await userProvider
                        .postDeleteUser(deleteUserHolder.toMap());
                    PsProgressDialog.dismissDialog();
                    if (_apiStatus.data != null) {
                      closeMoreContainerView();
                    } else {
                      showDialog<dynamic>(
                          context: context,
                          builder: (BuildContext context) {
                            return ErrorDialog(message: _apiStatus.message);
                          });
                    }
                  }
                  );
            });
      },
      child: Container(
        color: PsColors.backgroundColor,
        padding: const EdgeInsets.all(PsDimens.space12),
        child: Ink(
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: <Widget>[
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: <Widget>[
                  Text(
                    Utils.getString( 'more__deactivate_account_title'),
                    style: Theme.of(context).textTheme.subtitle1,
                  ),
                  const SizedBox(
                    height: PsDimens.space8,
                  ),
                  Text(
                    Utils.getString(
                        'more__recover_account_after_deactivate'),
                    style: Theme.of(context).textTheme.caption,
                  ),
                ],
              ),
              Icon(
                Icons.arrow_forward_ios,
                color: PsColors.mainColor,
                size: PsDimens.space12,
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _MorePostTitleWidget extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Container(
      color: PsColors.coreBackgroundColor,
      padding: const EdgeInsets.all(PsDimens.space12),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.start,
        children: <Widget>[
          Icon(Icons.format_list_bulleted, color: PsColors.mainColor),
          const SizedBox(
            width: PsDimens.space16,
          ),
          Text(
            Utils.getString('more__post_title'),
            softWrap: false,
            textAlign: TextAlign.left,
            style: Theme.of(context).textTheme.subtitle1!.copyWith(
                color: PsColors.mainColor, fontWeight: FontWeight.bold),
          ),
        ],
      ),
    );
  }
}

class _MorePendingPostWidget extends StatefulWidget {
  const _MorePendingPostWidget(
      {Key? key,
      required this.userId,
      required this.animationController,
      required this.headerTitle,
      required this.status,
      this.animation})
      : super(key: key);

  final String userId;
  final AnimationController animationController;
  final String headerTitle;
  final String status;
  final Animation<double>? animation;

  @override
  _MorePendingPostWidgetState createState() => _MorePendingPostWidgetState();
}

class _MorePendingPostWidgetState extends State<_MorePendingPostWidget> {
  UserProvider? userProvider;

  @override
  Widget build(BuildContext context) {
    userProvider = Provider.of<UserProvider>(context, listen: false);

    return GestureDetector(
      onTap: () {
        Navigator.pushNamed(context, RoutePaths.userItemList,
            arguments: UserItemListIntentHolder(
                userId: userProvider!.psValueHolder!.loginUserId,
                status: widget.status,
                title: widget.headerTitle));
      },
      child: Container(
        color: PsColors.backgroundColor,
        padding: const EdgeInsets.all(PsDimens.space12),
        child: Ink(
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: <Widget>[
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: <Widget>[
                  Text(
                    Utils.getString('more__pending_post_title'),
                    style: Theme.of(context).textTheme.subtitle1,
                  ),
                  const SizedBox(
                    height: PsDimens.space8,
                  ),
                  Text(
                    Utils.getString('more__pending_list'),
                    style: Theme.of(context).textTheme.caption,
                  ),
                ],
              ),
              Icon(
                Icons.arrow_forward_ios,
                color: PsColors.mainColor,
                size: PsDimens.space12,
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _MoreActivePostWidget extends StatefulWidget {
  const _MoreActivePostWidget(
      {Key? key,
      required this.userId,
      required this.animationController,
      required this.headerTitle,
      required this.status,
      this.animation})
      : super(key: key);

  final String userId;
  final AnimationController animationController;
  final String headerTitle;
  final String status;
  final Animation<double>? animation;

  @override
  _MoreActivePostWidgetState createState() => _MoreActivePostWidgetState();
}

class _MoreActivePostWidgetState extends State<_MoreActivePostWidget> {
  UserProvider? userProvider;

  @override
  Widget build(BuildContext context) {
    userProvider = Provider.of<UserProvider>(context, listen: false);

    return GestureDetector(
      onTap: () {
        Navigator.pushNamed(context, RoutePaths.userItemList,
            arguments: UserItemListIntentHolder(
                userId: userProvider!.psValueHolder!.loginUserId,
                status: widget.status,
                title: widget.headerTitle));
      },
      child: Container(
        color: PsColors.backgroundColor,
        padding: const EdgeInsets.all(PsDimens.space12),
        child: Ink(
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: <Widget>[
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: <Widget>[
                  Text(
                    Utils.getString('more__active_post_title'),
                    style: Theme.of(context).textTheme.subtitle1,
                  ),
                  const SizedBox(
                    height: PsDimens.space8,
                  ),
                  Text(
                    Utils.getString('more__search_active_post'),
                    style: Theme.of(context).textTheme.caption,
                  ),
                ],
              ),
              Icon(
                Icons.arrow_forward_ios,
                color: PsColors.mainColor,
                size: PsDimens.space12,
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _MoreFavouriteWidget extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: () {
        Navigator.pushNamed(context, RoutePaths.favouriteItemList);
      },
      child: Container(
        color: PsColors.backgroundColor,
        padding: const EdgeInsets.all(PsDimens.space12),
        child: Ink(
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: <Widget>[
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: <Widget>[
                  Text(
                    Utils.getString('more__favourite_title'),
                    style: Theme.of(context).textTheme.subtitle1,
                  ),
                  const SizedBox(
                    height: PsDimens.space8,
                  ),
                  Text(
                    Utils.getString('more__favourite_post'),
                    style: Theme.of(context).textTheme.caption,
                  ),
                ],
              ),
              Icon(
                Icons.arrow_forward_ios,
                color: PsColors.mainColor,
                size: PsDimens.space12,
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _MoreActivityTitleWidget extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Container(
      color: PsColors.coreBackgroundColor,
      padding: const EdgeInsets.all(PsDimens.space12),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.start,
        children: <Widget>[
          Icon(Icons.touch_app, color: PsColors.mainColor),
          const SizedBox(
            width: PsDimens.space16,
          ),
          Text(
            Utils.getString('more__activity_title'),
            softWrap: false,
            textAlign: TextAlign.left,
            style: Theme.of(context).textTheme.subtitle1!.copyWith(
                color: PsColors.mainColor, fontWeight: FontWeight.bold),
          ),
        ],
      ),
    );
  }
}

class _MorePaidAdsWidget extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: () {
        Navigator.pushNamed(context, RoutePaths.paidAdItemList);
      },
      child: Container(
        color: PsColors.backgroundColor,
        padding: const EdgeInsets.all(PsDimens.space12),
        child: Ink(
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: <Widget>[
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: <Widget>[
                  Text(
                    Utils.getString('more__paid_ads_title'),
                    style: Theme.of(context).textTheme.subtitle1,
                  ),
                  const SizedBox(
                    height: PsDimens.space8,
                  ),
                  Text(
                    Utils.getString('more__paid_ads_promote_list'),
                    style: Theme.of(context).textTheme.caption,
                  ),
                ],
              ),
              Icon(
                Icons.arrow_forward_ios,
                color: PsColors.mainColor,
                size: PsDimens.space12,
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _MoreUploadedItemWidget extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: () {
        Navigator.pushNamed(context, RoutePaths.uploaded);
      },
      child: Container(
        color: PsColors.backgroundColor,
        padding: const EdgeInsets.all(PsDimens.space12),
        child: Ink(
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: <Widget>[
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: <Widget>[
                  Text(
                    Utils.getString('home__menu_drawer_uploaded_item'),
                    style: Theme.of(context).textTheme.subtitle1,
                  ),
                  const SizedBox(
                    height: PsDimens.space8,
                  ),
                  Text(
                    Utils.getString('more__uploaded'),
                    style: Theme.of(context).textTheme.caption,
                  ),
                ],
              ),
              Icon(
                Icons.arrow_forward_ios,
                color: PsColors.mainColor,
                size: PsDimens.space12,
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _MoreHistoryteWidget extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: () {
        Navigator.pushNamed(context, RoutePaths.historyList);
      },
      child: Container(
        color: PsColors.backgroundColor,
        padding: const EdgeInsets.all(PsDimens.space12),
        child: Ink(
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: <Widget>[
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: <Widget>[
                  Text(
                    Utils.getString('more__history_title'),
                    style: Theme.of(context).textTheme.subtitle1,
                  ),
                  const SizedBox(
                    height: PsDimens.space8,
                  ),
                  Text(
                    Utils.getString('more__history_browse'),
                    style: Theme.of(context).textTheme.caption,
                  ),
                ],
              ),
              Icon(
                Icons.arrow_forward_ios,
                color: PsColors.mainColor,
                size: PsDimens.space12,
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _MoreSettingAndPrivacyTitleWidget extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return Container(
      color: PsColors.coreBackgroundColor,
      padding: const EdgeInsets.all(PsDimens.space12),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.start,
        children: <Widget>[
          Icon(Icons.settings, color: PsColors.mainColor),
          const SizedBox(
            width: PsDimens.space16,
          ),
          Text(
            Utils.getString('more__setting_and_privacy_title'),
            softWrap: false,
            textAlign: TextAlign.left,
            style: Theme.of(context).textTheme.subtitle1!.copyWith(
                color: PsColors.mainColor, fontWeight: FontWeight.bold),
          ),
        ],
      ),
    );
  }
}

// class _MoreBlockUserWidget extends StatelessWidget {
//   @override
//   Widget build(BuildContext context) {
//     return InkWell(
//       onTap: () {
//         Navigator.pushNamed(context, RoutePaths.ratingList);
//       },
//       child: Container(
//         color: PsColors.backgroundColor,
//         padding: const EdgeInsets.all(PsDimens.space12),
//         child: Ink(
//           child: Row(
//             mainAxisAlignment: MainAxisAlignment.spaceBetween,
//             children: <Widget>[
//               Column(
//                 crossAxisAlignment: CrossAxisAlignment.start,
//                 children: <Widget>[
//                   Text(
//                     Utils.getString(context, 'profile__more'),
//                     style: Theme.of(context).textTheme.subtitle1,
//                   ),
//                   const SizedBox(
//                     height: PsDimens.space8,
//                   ),
//                   Text(
//                     Utils.getString(context, 'profile__more'),
//                     style: Theme.of(context).textTheme.caption,
//                   ),
//                 ],
//               ),
//               Icon(
//                 Icons.arrow_forward_ios,
//                 color: PsColors.mainColor,
//                 size: PsDimens.space12,
//               ),
//             ],
//           ),
//         ),
//       ),
//     );
//   }
// }

// class _MoreReportItemWidget extends StatelessWidget {
//   @override
//   Widget build(BuildContext context) {
//     return InkWell(
//       onTap: () {
//         Navigator.pushNamed(context, RoutePaths.ratingList);
//       },
//       child: Container(
//         color: PsColors.backgroundColor,
//         padding: const EdgeInsets.all(PsDimens.space12),
//         child: Ink(
//           child: Row(
//             mainAxisAlignment: MainAxisAlignment.spaceBetween,
//             children: <Widget>[
//               Column(
//                 crossAxisAlignment: CrossAxisAlignment.start,
//                 children: <Widget>[
//                   Text(
//                     Utils.getString(context, 'profile__more'),
//                     style: Theme.of(context).textTheme.subtitle1,
//                   ),
//                   const SizedBox(
//                     height: PsDimens.space8,
//                   ),
//                   Text(
//                     Utils.getString(context, 'profile__more'),
//                     style: Theme.of(context).textTheme.caption,
//                   ),
//                 ],
//               ),
//               Icon(
//                 Icons.arrow_forward_ios,
//                 color: PsColors.mainColor,
//                 size: PsDimens.space12,
//               ),
//             ],
//           ),
//         ),
//       ),
//     );
//   }
// }

// class _MoreDeactivateWidget extends StatelessWidget {
//   const _MoreDeactivateWidget(
//       {@required this.userProvider, @required this.callLogoutCallBack});

//   final UserProvider userProvider;
//   final Function callLogoutCallBack;

//   @override
//   Widget build(BuildContext context) {
//     return InkWell(
//       onTap: () {
//         showDialog<dynamic>(
//             context: context,
//             builder: (BuildContext context) {
//               return ConfirmDialogView(
//                   description: Utils.getString(
//                       context, 'profile__deactivate_confirm_text'),
//                   leftButtonText: Utils.getString(context, 'dialog__cancel'),
//                   rightButtonText: Utils.getString(context, 'dialog__ok'),
//                   onAgreeTap: () async {
//                     // final DeleteUserHolder deleteUserHolder = DeleteUserHolder(
//                     //     userId: userProvider.psValueHolder.loginUserId);
//                     // final PsResource<ApiStatus> _apiStatus = await userProvider
//                     //     .postDeleteUser(deleteUserHolder.toMap());
//                     // if (_apiStatus.data != null) {
//                     //   if (callLogoutCallBack != null) {
//                     //     callLogoutCallBack(
//                     //         userProvider.psValueHolder.loginUserId);
//                     //   }
//                   }
//                   //}
//                   );
//             });
//       },
//       child: Container(
//         color: PsColors.backgroundColor,
//         padding: const EdgeInsets.all(PsDimens.space12),
//         child: Ink(
//           child: Row(
//             mainAxisAlignment: MainAxisAlignment.spaceBetween,
//             children: <Widget>[
//               Column(
//                 crossAxisAlignment: CrossAxisAlignment.start,
//                 children: <Widget>[
//                   Text(
//                     Utils.getString(context, 'more__deactivate_account_title'),
//                     style: Theme.of(context).textTheme.subtitle1,
//                   ),
//                   const SizedBox(
//                     height: PsDimens.space8,
//                   ),
//                   Text(
//                     Utils.getString(
//                         context, 'more__recover_account_after_deactivate'),
//                     style: Theme.of(context).textTheme.caption,
//                   ),
//                 ],
//               ),
//               Icon(
//                 Icons.arrow_forward_ios,
//                 color: PsColors.mainColor,
//                 size: PsDimens.space12,
//               ),
//             ],
//           ),
//         ),
//       ),
//     );
//   }
// }

class _MoreSettingWidget extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: () {
        Navigator.pushNamed(context, RoutePaths.setting);
      },
      child: Container(
        color: PsColors.backgroundColor,
        padding: const EdgeInsets.all(PsDimens.space12),
        child: Ink(
          child: Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: <Widget>[
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: <Widget>[
                  Text(
                    Utils.getString('setting__toolbar_name'),
                    style: Theme.of(context).textTheme.subtitle1,
                  ),
                  const SizedBox(
                    height: PsDimens.space8,
                  ),
                  Text(
                    Utils.getString('more__app_setting'),
                    style: Theme.of(context).textTheme.caption,
                  ),
                ],
              ),
              Icon(
                Icons.arrow_forward_ios,
                color: PsColors.mainColor,
                size: PsDimens.space12,
              ),
            ],
          ),
        ),
      ),
    );
  }
}
