package com.nusatim.partner.core.data.repository

import com.nusatim.partner.core.domain.repository.NotificationsRepository
import com.nusatim.partner.core.data.repository.BaseRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.network.NotificationsApiService
import com.nusatim.partner.core.model.dto.NotificationResponse
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import com.nusatim.partner.core.model.dto.UnreadCountResponse
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class NotificationsRepositoryImpl @Inject constructor(
    private val apiService: NotificationsApiService
) : BaseRepository(), NotificationsRepository {

    override fun getNotifications(
        page: Int?,
        perPage: Int?
    ): Flow<ResultState<PagedBaseResponse<NotificationResponse>>> = safeNetworkCall {
        apiService.getNotifications(page, perPage)
    }

    override fun getUnreadCount(): Flow<ResultState<UnreadCountResponse>> = safeNetworkCall {
        apiService.getUnreadCount()
    }

    override fun markAsRead(id: String): Flow<ResultState<NotificationResponse>> = safeNetworkCall {
        apiService.markAsRead(id).data
    }

    override fun markAllAsRead(): Flow<ResultState<String>> = safeNetworkCall {
        apiService.markAllAsRead()["message"] ?: "Success"
    }
}
