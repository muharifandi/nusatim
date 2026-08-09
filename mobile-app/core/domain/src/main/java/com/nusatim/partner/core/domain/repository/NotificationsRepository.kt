package com.nusatim.partner.core.domain.repository

import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.NotificationResponse
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import com.nusatim.partner.core.model.dto.UnreadCountResponse
import kotlinx.coroutines.flow.Flow

interface NotificationsRepository {
    fun getNotifications(
        page: Int? = null,
        perPage: Int? = null
    ): Flow<ResultState<PagedBaseResponse<NotificationResponse>>>

    fun getUnreadCount(): Flow<ResultState<UnreadCountResponse>>

    fun markAsRead(id: String): Flow<ResultState<NotificationResponse>>

    fun markAllAsRead(): Flow<ResultState<String>>
}
