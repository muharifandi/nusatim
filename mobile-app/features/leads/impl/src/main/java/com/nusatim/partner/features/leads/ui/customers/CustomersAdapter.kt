package com.nusatim.partner.features.leads.ui.customers

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import com.nusatim.partner.core.model.dto.CustomerResponse
import com.nusatim.partner.features.leads.databinding.ItemCustomerBinding
import java.text.NumberFormat
import java.util.*

class CustomersAdapter(
    private val onItemClick: (CustomerResponse) -> Unit
) : ListAdapter<CustomerResponse, CustomersAdapter.ViewHolder>(DiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemCustomerBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return ViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    inner class ViewHolder(private val binding: ItemCustomerBinding) : RecyclerView.ViewHolder(binding.root) {
        init {
            binding.root.setOnClickListener {
                val pos = adapterPosition
                if (pos != RecyclerView.NO_POSITION) onItemClick(getItem(pos))
            }
        }

        fun bind(item: CustomerResponse) {
            binding.tvName.text = item.name ?: "-"
            binding.tvProduct.text = item.serviceName ?: "-"
            binding.tvProjectValue.text = formatRupiah(item.projectValue)
            
            // Payment Status Badge
            val (statusText, statusColor, statusBg, statusIcon) = when(item.paymentStatus) {
                "unpaid" -> Quadruple(
                    binding.root.context.getString(com.nusatim.partner.features.leads.R.string.customer_status_unpaid),
                    com.nusatim.partner.core.ui.R.color.status_warning,
                    com.nusatim.partner.core.ui.R.color.project_orange_light,
                    com.nusatim.partner.core.ui.R.drawable.ic_wallet_small
                )
                "partial" -> Quadruple(
                    binding.root.context.getString(com.nusatim.partner.features.leads.R.string.customer_status_partial),
                    com.nusatim.partner.core.ui.R.color.status_info,
                    com.nusatim.partner.core.ui.R.color.project_blue_light,
                    android.R.drawable.ic_menu_myplaces
                )
                "paid" -> Quadruple(
                    binding.root.context.getString(com.nusatim.partner.features.leads.R.string.customer_status_paid),
                    com.nusatim.partner.core.ui.R.color.status_success,
                    com.nusatim.partner.core.ui.R.color.project_green_light,
                    com.nusatim.partner.core.ui.R.drawable.ic_wallet_small
                )
                else -> Quadruple(
                    item.paymentStatus ?: "-",
                    com.nusatim.partner.core.ui.R.color.status_neutral,
                    com.nusatim.partner.core.ui.R.color.partner_surface_variant,
                    com.nusatim.partner.core.ui.R.drawable.ic_status_pending
                )
            }
            
            binding.tvStatusText.text = statusText
            binding.tvStatusText.setTextColor(androidx.core.content.ContextCompat.getColor(binding.root.context, statusColor))
            binding.ivStatusIcon.setImageResource(statusIcon)
            binding.ivStatusIcon.imageTintList = android.content.res.ColorStateList.valueOf(
                androidx.core.content.ContextCompat.getColor(binding.root.context, statusColor)
            )
            binding.cardStatus.backgroundTintList = android.content.res.ColorStateList.valueOf(
                androidx.core.content.ContextCompat.getColor(binding.root.context, statusBg)
            )

            // Icon Background
            val iconBg = if (item.paymentStatus == "paid") {
                com.nusatim.partner.core.ui.R.color.project_green_light
            } else {
                com.nusatim.partner.core.ui.R.color.project_orange_light
            }
            binding.cardIcon.backgroundTintList = android.content.res.ColorStateList.valueOf(
                androidx.core.content.ContextCompat.getColor(binding.root.context, iconBg)
            )
            binding.cardWallet.backgroundTintList = android.content.res.ColorStateList.valueOf(
                androidx.core.content.ContextCompat.getColor(binding.root.context, iconBg)
            )

            // Project Progress
            val project = item.project
            if (project != null) {
                binding.groupProgress.visibility = View.VISIBLE
                binding.progressProject.progress = project.progress
                binding.tvProgressPercent.text = binding.root.context.getString(
                    com.nusatim.partner.features.leads.R.string.customer_label_percent,
                    project.progress
                )
            } else {
                binding.groupProgress.visibility = View.GONE
            }
        }
    }

    private data class Quadruple<out A, out B, out C, out D>(
        val first: A,
        val second: B,
        val third: C,
        val fourth: D
    )

    private fun formatRupiah(amount: Long): String {
        val format = NumberFormat.getCurrencyInstance(Locale("id", "ID"))
        return format.format(amount).replace(",00", "")
    }

    class DiffCallback : DiffUtil.ItemCallback<CustomerResponse>() {
        override fun areItemsTheSame(oldItem: CustomerResponse, newItem: CustomerResponse): Boolean = oldItem.id == newItem.id
        override fun areContentsTheSame(oldItem: CustomerResponse, newItem: CustomerResponse): Boolean = oldItem == newItem
    }
}
