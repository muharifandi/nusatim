package com.nusatim.partner.core.model.dto

import com.google.gson.annotations.SerializedName

data class WithdrawalResponse(
    @SerializedName("id") val id: Int,
    @SerializedName("amount") val amount: Long,
    @SerializedName("bank_name") val bankName: String,
    @SerializedName("bank_account_number") val bankAccountNumber: String,
    @SerializedName("bank_account_holder") val bankAccountHolder: String,
    @SerializedName("note") val note: String?,
    @SerializedName("status") val status: String?,
    @SerializedName("rejection_reason") val rejectionReason: String?,
    @SerializedName("ktp_url") val ktpUrl: String,
    @SerializedName("proof_of_transfer_url") val proofOfTransferUrl: String?,
    @SerializedName("created_at") val createdAt: String
)

data class WithdrawalBalanceResponse(
    @SerializedName("available_balance") val availableBalance: Long,
    @SerializedName("minimum_withdrawal") val minimumWithdrawal: Long
)
