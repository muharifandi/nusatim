package com.nusatim.partner.core.network

import com.nusatim.partner.core.model.dto.BaseResponse
import com.nusatim.partner.core.model.dto.NotificationResponse
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import com.nusatim.partner.core.model.dto.UnreadCountResponse
import retrofit2.http.*

interface NotificationsApiService {

    @GET("notifications")
    suspend fun getNotifications(
        @Query("page") page: Int? = null,
        @Query("per_page") perPage: Int? = null
    ): PagedBaseResponse<NotificationResponse>

    @GET("notifications/unread-count")
    suspend fun getUnreadCount(): UnreadCountResponse

    @PATCH("notifications/{id}/read")
    suspend fun markAsRead(@Path("id") id: String): BaseResponse<NotificationResponse>

    @PATCH("notifications/read-all")
    suspend fun markAllAsRead(): Map<String, String>
}
