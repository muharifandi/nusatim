package com.nusatim.partner.features.leads.ui.leads

import android.view.LayoutInflater
import android.view.ViewGroup
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import com.nusatim.partner.core.model.dto.LeadResponse
import com.nusatim.partner.features.leads.databinding.ItemLeadBinding
import java.text.NumberFormat
import java.util.*

class LeadsAdapter(
    private val onItemClick: (LeadResponse) -> Unit
) : ListAdapter<LeadResponse, LeadsAdapter.ViewHolder>(DiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemLeadBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return ViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    inner class ViewHolder(private val binding: ItemLeadBinding) : RecyclerView.ViewHolder(binding.root) {
        init {
            binding.root.setOnClickListener {
                val position = adapterPosition
                if (position != RecyclerView.NO_POSITION) {
                    onItemClick(getItem(position))
                }
            }
        }

        fun bind(item: LeadResponse) {
            binding.tvName.text = item.name ?: "-"
            binding.tvPhone.text = item.phone ?: "-"
            binding.tvProduct.text = item.serviceName
            binding.tvEstimation.text = formatRupiah(item.estimatedValue ?: 0)
            
            // Status Mapping
            binding.tvStatusText.text = item.status?.replaceFirstChar { it.uppercase() } ?: "-"
            
            val (statusColor, statusBg, iconBg) = when (item.status?.lowercase()) {
                "new", "pending", "open", "draft" -> Triple(
                    com.nusatim.partner.core.ui.R.color.status_neutral,
                    com.nusatim.partner.core.ui.R.color.partner_surface_variant,
                    com.nusatim.partner.core.ui.R.color.project_orange_light
                )
                "contacted" -> Triple(
                    com.nusatim.partner.core.ui.R.color.status_info,
                    com.nusatim.partner.core.ui.R.color.project_blue_light,
                    com.nusatim.partner.core.ui.R.color.project_blue_light
                )
                "negotiation", "proposal" -> Triple(
                    com.nusatim.partner.core.ui.R.color.status_info,
                    com.nusatim.partner.core.ui.R.color.partner_surface_variant,
                    com.nusatim.partner.core.ui.R.color.partner_surface_variant
                )
                "lost", "rejected" -> Triple(
                    com.nusatim.partner.core.ui.R.color.partner_error,
                    com.nusatim.partner.core.ui.R.color.partner_surface_variant,
                    com.nusatim.partner.core.ui.R.color.project_orange_light
                )
                "won", "closed" -> Triple(
                    com.nusatim.partner.core.ui.R.color.status_success,
                    com.nusatim.partner.core.ui.R.color.project_green_light,
                    com.nusatim.partner.core.ui.R.color.project_green_light
                )
                else -> Triple(
                    com.nusatim.partner.core.ui.R.color.status_info,
                    com.nusatim.partner.core.ui.R.color.project_blue_light,
                    com.nusatim.partner.core.ui.R.color.project_blue_light
                )
            }
            
            val context = binding.root.context
            val color = androidx.core.content.ContextCompat.getColor(context, statusColor)
            
            binding.ivStatusDot.imageTintList = android.content.res.ColorStateList.valueOf(color)
            binding.tvStatusText.setTextColor(color)
            binding.cardStatus.backgroundTintList = android.content.res.ColorStateList.valueOf(
                androidx.core.content.ContextCompat.getColor(context, statusBg)
            )
            
            // Icon Styling
            binding.cardIcon.backgroundTintList = android.content.res.ColorStateList.valueOf(
                androidx.core.content.ContextCompat.getColor(context, iconBg)
            )
            binding.ivIcon.imageTintList = android.content.res.ColorStateList.valueOf(
                androidx.core.content.ContextCompat.getColor(context, com.nusatim.partner.core.ui.R.color.partner_primary)
            )
            
            // Product Styling
            binding.ivTag.imageTintList = android.content.res.ColorStateList.valueOf(
                androidx.core.content.ContextCompat.getColor(context, com.nusatim.partner.core.ui.R.color.partner_primary)
            )
        }
    }

    private fun formatRupiah(amount: Long): String {
        val format = NumberFormat.getCurrencyInstance(Locale("id", "ID"))
        return format.format(amount).replace(",00", "")
    }

    class DiffCallback : DiffUtil.ItemCallback<LeadResponse>() {
        override fun areItemsTheSame(oldItem: LeadResponse, newItem: LeadResponse): Boolean {
            return oldItem.id == newItem.id
        }

        override fun areContentsTheSame(oldItem: LeadResponse, newItem: LeadResponse): Boolean {
            return oldItem == newItem
        }
    }
}
