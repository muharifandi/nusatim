package com.nusatim.partner.features.home.ui.notifications.state

import com.nusatim.partner.core.architecture.mvi.UiEffect

sealed interface NotificationsEffect : UiEffect {
    data class ShowError(val message: String) : NotificationsEffect
}
