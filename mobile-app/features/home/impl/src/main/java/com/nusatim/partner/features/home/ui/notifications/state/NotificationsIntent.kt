package com.nusatim.partner.features.home.ui.notifications.state

import com.nusatim.partner.core.architecture.mvi.UiIntent

sealed interface NotificationsIntent : UiIntent {
    data class LoadNotifications(val page: Int = 1) : NotificationsIntent
    data object LoadUnreadCount : NotificationsIntent
    data class MarkAsRead(val id: String) : NotificationsIntent
    data object MarkAllAsRead : NotificationsIntent
}
