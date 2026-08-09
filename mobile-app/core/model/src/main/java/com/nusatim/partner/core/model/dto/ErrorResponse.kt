package com.nusatim.partner.core.model.dto

import com.google.gson.annotations.SerializedName

/**
 * Created by Foundation Team
 * Representasi objek error dari NewsAPI sesuai dokumentasi.
 */
data class ErrorResponse(
    @SerializedName("message") val message: String,
    @SerializedName("errors") val errors: Map<String, List<String>>? = null,
    @SerializedName("status") val status: String? = null,
    @SerializedName("rejection_reason") val rejectionReason: String? = null
)
