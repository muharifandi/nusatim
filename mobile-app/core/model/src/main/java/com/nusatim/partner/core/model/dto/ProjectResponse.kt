package com.nusatim.partner.core.model.dto

import com.google.gson.annotations.SerializedName

data class ProjectResponse(
    @SerializedName("id") val id: Int,
    @SerializedName("name") val name: String,
    @SerializedName("description") val description: String,
    @SerializedName("service_id") val serviceId: Int?,
    @SerializedName("service_name") val serviceName: String?,
    @SerializedName("budget") val budget: Long,
    @SerializedName("location") val location: String,
    @SerializedName("deadline") val deadline: String,
    @SerializedName("difficulty") val difficulty: String,
    @SerializedName("commission_value") val commissionValue: Long,
    @SerializedName("status") val status: String,
    @SerializedName("progress") val progress: Int?,
    @SerializedName("is_mine") val isMine: Boolean,
    @SerializedName("claimed_at") val claimedAt: String?
)
