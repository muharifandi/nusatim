package com.nusatim.partner.core.model.dto

import com.google.gson.annotations.SerializedName

data class PartnerResponse(
    @SerializedName("id") val id: Int,
    @SerializedName("name") val name: String,
    @SerializedName("email") val email: String,
    @SerializedName("status") val status: String,
    @SerializedName("level") val level: String?,
    @SerializedName("rejection_reason") val rejectionReason: String?,
    @SerializedName("bank_name") val bankName: String,
    @SerializedName("bank_account_number") val bankAccountNumber: String,
    @SerializedName("bank_account_holder") val bankAccountHolder: String,
    @SerializedName("email_notifications_enabled") val emailNotificationsEnabled: Boolean,
    @SerializedName("agreement_accepted_at") val agreementAcceptedAt: String,
    @SerializedName("profile_photo_url") val profilePhotoUrl: String,
    @SerializedName("ktp_url") val ktpUrl: String,
    @SerializedName("npwp_url") val npwpUrl: String?,
    @SerializedName("created_at") val createdAt: String
)

data class BaseResponse<T>(
    @SerializedName("data") val data: T
)

data class LoginResponse(
    @SerializedName("token") val token: String,
    @SerializedName("partner") val partner: PartnerResponse
)
