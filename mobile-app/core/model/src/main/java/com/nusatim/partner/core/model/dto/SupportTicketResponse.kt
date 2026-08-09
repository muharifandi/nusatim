package com.nusatim.partner.core.model.dto

import com.google.gson.annotations.SerializedName

data class SupportTicketResponse(
    @SerializedName("id") val id: Int,
    @SerializedName("subject") val subject: String,
    @SerializedName("description") val description: String,
    @SerializedName("status") val status: String,
    @SerializedName("resolution_note") val resolutionNote: String?,
    @SerializedName("created_at") val createdAt: String
)
