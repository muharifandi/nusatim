package com.nusatim.partner.features.finance.ui.commissions

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import com.nusatim.partner.core.model.dto.CommissionResponse
import com.nusatim.partner.features.finance.databinding.ItemCommissionBinding
import java.text.NumberFormat
import java.util.*

class CommissionsAdapter(
    private val onItemClick: (CommissionResponse) -> Unit
) : ListAdapter<CommissionResponse, CommissionsAdapter.ViewHolder>(DiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemCommissionBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return ViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    inner class ViewHolder(private val binding: ItemCommissionBinding) : RecyclerView.ViewHolder(binding.root) {
        init {
            binding.root.setOnClickListener {
                val pos = adapterPosition
                if (pos != RecyclerView.NO_POSITION) onItemClick(getItem(pos))
            }
        }

        fun bind(item: CommissionResponse) {
            binding.tvCustomerName.text = item.customerName ?: "Bonus"
            binding.tvServiceName.text = item.serviceName ?: "-"
            binding.tvAmount.text = formatRupiah(item.amount)
            binding.tvDate.text = formatDateTime(item.createdAt)
            
            binding.ivBonus.visibility = if (item.isBonus) View.VISIBLE else View.GONE
            
            // Status Badge
            binding.tvStatusText.text = item.status?.replaceFirstChar { it.uppercase() } ?: "-"
            
            val (statusColor, statusBg, statusIcon) = when (item.status?.lowercase()) {
                "pending", "waiting_client_payment" -> Triple(
                    com.nusatim.partner.core.ui.R.color.status_warning,
                    com.nusatim.partner.core.ui.R.color.project_orange_light,
                    com.nusatim.partner.core.ui.R.drawable.ic_status_pending
                )
                "approved" -> Triple(
                    com.nusatim.partner.core.ui.R.color.status_info,
                    com.nusatim.partner.core.ui.R.color.project_blue_light,
                    com.nusatim.partner.core.ui.R.drawable.ic_check_circle
                )
                "paid" -> Triple(
                    com.nusatim.partner.core.ui.R.color.status_success,
                    com.nusatim.partner.core.ui.R.color.project_green_light,
                    com.nusatim.partner.core.ui.R.drawable.ic_check_circle
                )
                "rejected" -> Triple(
                    com.nusatim.partner.core.ui.R.color.partner_error,
                    com.nusatim.partner.core.ui.R.color.partner_surface_variant,
                    com.nusatim.partner.core.ui.R.drawable.ic_status_rejected
                )
                else -> Triple(
                    com.nusatim.partner.core.ui.R.color.status_neutral,
                    com.nusatim.partner.core.ui.R.color.partner_surface_variant,
                    com.nusatim.partner.core.ui.R.drawable.ic_status_pending
                )
            }
            
            binding.cardStatus.backgroundTintList = android.content.res.ColorStateList.valueOf(
                androidx.core.content.ContextCompat.getColor(binding.root.context, statusBg)
            )
            binding.tvStatusText.setTextColor(
                androidx.core.content.ContextCompat.getColor(binding.root.context, statusColor)
            )
            binding.ivStatusIcon.setImageResource(statusIcon)
            binding.ivStatusIcon.imageTintList = android.content.res.ColorStateList.valueOf(
                androidx.core.content.ContextCompat.getColor(binding.root.context, statusColor)
            )
            
            // Icon backgrounds
            val (iconBg, iconTint) = if (item.isBonus || item.status?.lowercase() == "paid") {
                Pair(
                    com.nusatim.partner.core.ui.R.color.project_green_light,
                    com.nusatim.partner.core.ui.R.color.status_success
                )
            } else {
                Pair(
                    com.nusatim.partner.core.ui.R.color.project_orange_light,
                    com.nusatim.partner.core.ui.R.color.partner_primary
                )
            }
            
            binding.cardTypeIcon.backgroundTintList = android.content.res.ColorStateList.valueOf(
                androidx.core.content.ContextCompat.getColor(binding.root.context, iconBg)
            )
            binding.cardWalletIcon.backgroundTintList = android.content.res.ColorStateList.valueOf(
                androidx.core.content.ContextCompat.getColor(binding.root.context, iconBg)
            )

            binding.ivTypeIcon.imageTintList = android.content.res.ColorStateList.valueOf(
                androidx.core.content.ContextCompat.getColor(binding.root.context, iconTint)
            )
            
            // Type Icon
            if (item.customerName != null) {
                binding.ivTypeIcon.setImageResource(android.R.drawable.ic_menu_myplaces)
            } else {
                binding.ivTypeIcon.setImageResource(com.nusatim.partner.core.ui.R.drawable.ic_bag_small)
            }
        }
    }

    private fun formatRupiah(amount: Long): String {
        val format = NumberFormat.getCurrencyInstance(Locale("id", "ID"))
        return format.format(amount).replace(",00", "")
    }

    private fun formatDateTime(createdAt: String): String {
        return try {
            // Raw: 2026-08-08 05:51:40.000000Z
            // If it contains space, split it
            if (createdAt.contains(" ")) {
                val parts = createdAt.split(" ")
                "${parts[0]}\n${parts[1]}"
            } else {
                createdAt.replace("T", "\n")
            }
        } catch (e: Exception) {
            createdAt
        }
    }

    class DiffCallback : DiffUtil.ItemCallback<CommissionResponse>() {
        override fun areItemsTheSame(oldItem: CommissionResponse, newItem: CommissionResponse): Boolean = oldItem.id == newItem.id
        override fun areContentsTheSame(oldItem: CommissionResponse, newItem: CommissionResponse): Boolean = oldItem == newItem
    }
}
