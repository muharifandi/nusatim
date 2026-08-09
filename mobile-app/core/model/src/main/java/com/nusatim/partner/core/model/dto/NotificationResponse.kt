package com.nusatim.partner.core.model.dto

import com.google.gson.annotations.SerializedName

data class NotificationResponse(
    @SerializedName("id") val id: String,
    @SerializedName("title") val title: String,
    @SerializedName("body") val body: String?,
    @SerializedName("read_at") val readAt: String?,
    @SerializedName("created_at") val createdAt: String
)

data class UnreadCountResponse(
    @SerializedName("unread_count") val unreadCount: Int
)
