package com.nusatim.partner.features.home.ui.notifications.state

import com.nusatim.partner.core.architecture.mvi.UiState
import com.nusatim.partner.core.model.dto.NotificationResponse
import com.nusatim.partner.core.model.dto.PagedBaseResponse

data class NotificationsState(
    val isLoading: Boolean = false,
    val notificationsResponse: PagedBaseResponse<NotificationResponse>? = null,
    val unreadCount: Int = 0,
    val error: String? = null
) : UiState
