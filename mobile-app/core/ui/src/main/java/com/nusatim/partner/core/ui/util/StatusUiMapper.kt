package com.nusatim.partner.core.ui.util

import android.content.Context
import androidx.annotation.ColorRes
import androidx.annotation.StringRes
import androidx.core.content.ContextCompat
import com.nusatim.partner.core.common.util.Constants.Status
import com.nusatim.partner.core.ui.R

object StatusUiMapper {

    data class StatusUi(
        @StringRes val labelRes: Int,
        @ColorRes val colorRes: Int,
        @ColorRes val backgroundRes: Int = R.color.partner_surface_variant
    )

    fun mapStatus(status: String?): StatusUi {
        return when (status?.lowercase()) {
            Status.NEW, Status.OPEN, Status.DRAFT -> StatusUi(
                R.string.status_new,
                R.color.status_neutral,
                R.color.project_orange_light
            )
            Status.PENDING -> StatusUi(
                R.string.status_pending,
                R.color.status_neutral,
                R.color.project_orange_light
            )
            Status.PENDING_REVIEW, Status.PENDING_APPROVAL -> StatusUi(
                R.string.status_pending_approval,
                R.color.status_warning,
                R.color.project_orange_light
            )
            Status.CONTACTED -> StatusUi(
                R.string.status_contacted,
                R.color.status_info,
                R.color.project_blue_light
            )
            Status.QUALIFIED -> StatusUi(
                R.string.status_qualified,
                R.color.status_info,
                R.color.project_blue_light
            )
            Status.OPPORTUNITY -> StatusUi(
                R.string.status_opportunity,
                R.color.status_info,
                R.color.project_blue_light
            )
            Status.PROPOSAL -> StatusUi(
                R.string.status_proposal,
                R.color.status_info,
                R.color.project_blue_light
            )
            Status.NEGOTIATION -> StatusUi(
                R.string.status_negotiation,
                R.color.status_info,
                R.color.project_blue_light
            )
            Status.ASSIGNED -> StatusUi(
                R.string.status_assigned,
                R.color.status_info,
                R.color.project_blue_light
            )
            Status.IN_PROGRESS -> StatusUi(
                R.string.status_in_progress,
                R.color.status_warning,
                R.color.project_orange_light
            )
            Status.WAITING_CLIENT_PAYMENT, Status.WAITING_PAYMENT -> StatusUi(
                R.string.status_waiting_payment,
                R.color.status_warning,
                R.color.project_orange_light
            )
            Status.WON -> StatusUi(
                R.string.status_won,
                R.color.status_success,
                R.color.project_green_light
            )
            Status.PAID -> StatusUi(
                R.string.status_paid,
                R.color.status_success,
                R.color.project_green_light
            )
            Status.CLOSED, Status.RESOLVED -> StatusUi(
                R.string.status_closed,
                R.color.status_success,
                R.color.project_green_light
            )
            Status.APPROVED -> StatusUi(
                R.string.status_approved,
                R.color.status_success,
                R.color.project_green_light
            )
            Status.LOST, Status.REJECTED -> StatusUi(
                R.string.status_lost,
                R.color.partner_error,
                R.color.partner_error_container
            )
            Status.CANCELLED -> StatusUi(
                R.string.status_cancelled,
                R.color.partner_error,
                R.color.partner_error_container
            )
            Status.SUSPENDED -> StatusUi(
                R.string.status_suspended,
                R.color.partner_error,
                R.color.partner_error_container
            )
            else -> StatusUi(
                R.string.status_pending,
                R.color.status_neutral,
                R.color.project_orange_light
            )
        }
    }

    fun getLabel(context: Context, status: String?): String {
        return context.getString(mapStatus(status).labelRes)
    }

    fun getColor(context: Context, status: String?): Int {
        return ContextCompat.getColor(context, mapStatus(status).colorRes)
    }
}
