package com.nusatim.partner.features.projects.ui.board

import android.content.res.ColorStateList
import android.text.Spannable
import android.text.SpannableStringBuilder
import android.text.style.ForegroundColorSpan
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.core.content.ContextCompat
import androidx.recyclerview.widget.DiffUtil
import androidx.recyclerview.widget.ListAdapter
import androidx.recyclerview.widget.RecyclerView
import com.nusatim.partner.core.model.dto.ProjectResponse
import com.nusatim.partner.features.projects.databinding.ItemProjectBinding
import java.text.NumberFormat
import java.util.*

class ProjectsAdapter(
    private val onItemClick: (ProjectResponse) -> Unit,
    private val onClaimClick: (ProjectResponse) -> Unit
) : ListAdapter<ProjectResponse, ProjectsAdapter.ViewHolder>(DiffCallback()) {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val binding = ItemProjectBinding.inflate(LayoutInflater.from(parent.context), parent, false)
        return ViewHolder(binding)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(getItem(position))
    }

    inner class ViewHolder(private val binding: ItemProjectBinding) : RecyclerView.ViewHolder(binding.root) {
        init {
            binding.root.setOnClickListener {
                val pos = adapterPosition
                if (pos != RecyclerView.NO_POSITION) onItemClick(getItem(pos))
            }
            binding.btnClaim.setOnClickListener {
                val pos = adapterPosition
                if (pos != RecyclerView.NO_POSITION) onClaimClick(getItem(pos))
            }
        }

        fun bind(item: ProjectResponse) {
            binding.tvName.text = item.name
            binding.tvBudget.text = "Budget: ${formatRupiah(item.budget)}"
            
            // Commission with green value
            val commissionLabel = "Komisi: "
            val commissionValue = formatRupiah(item.commissionValue)
            val fullCommissionText = commissionLabel + commissionValue
            val spannable = SpannableStringBuilder(fullCommissionText)
            val greenColor = ContextCompat.getColor(binding.root.context, com.nusatim.partner.core.ui.R.color.status_success)
            spannable.setSpan(
                ForegroundColorSpan(greenColor),
                commissionLabel.length,
                fullCommissionText.length,
                Spannable.SPAN_EXCLUSIVE_EXCLUSIVE
            )
            binding.tvCommission.text = spannable
            
            binding.tvLocation.text = item.location
            binding.tvDeadline.text = item.deadline

            binding.tvStatus.text = item.status.replaceFirstChar { it.uppercase() }
            
            val (bgColor, textColor, dotColor) = when (item.status.lowercase()) {
                "available" -> Triple(
                    com.nusatim.partner.core.ui.R.color.project_blue_light,
                    com.nusatim.partner.core.ui.R.color.status_info,
                    com.nusatim.partner.core.ui.R.color.status_info
                )
                "pending_approval" -> Triple(
                    com.nusatim.partner.core.ui.R.color.project_orange_light,
                    com.nusatim.partner.core.ui.R.color.status_warning,
                    com.nusatim.partner.core.ui.R.color.status_warning
                )
                "assigned", "in_progress" -> Triple(
                    com.nusatim.partner.core.ui.R.color.project_green_light,
                    com.nusatim.partner.core.ui.R.color.status_success,
                    com.nusatim.partner.core.ui.R.color.status_success
                )
                else -> Triple(
                    com.nusatim.partner.core.ui.R.color.partner_silver,
                    com.nusatim.partner.core.ui.R.color.status_neutral,
                    com.nusatim.partner.core.ui.R.color.status_neutral
                )
            }
            
            binding.layoutStatus.backgroundTintList = ColorStateList.valueOf(
                ContextCompat.getColor(binding.root.context, bgColor)
            )
            binding.tvStatus.setTextColor(ContextCompat.getColor(binding.root.context, textColor))
            binding.ivStatusDot.imageTintList = ColorStateList.valueOf(
                ContextCompat.getColor(binding.root.context, dotColor)
            )

            binding.btnClaim.visibility = if (item.status == "available") View.VISIBLE else View.GONE
        }
    }

    private fun formatRupiah(amount: Long): String {
        val format = NumberFormat.getCurrencyInstance(Locale("id", "ID"))
        return format.format(amount).replace(",00", "")
    }

    class DiffCallback : DiffUtil.ItemCallback<ProjectResponse>() {
        override fun areItemsTheSame(oldItem: ProjectResponse, newItem: ProjectResponse): Boolean = oldItem.id == newItem.id
        override fun areContentsTheSame(oldItem: ProjectResponse, newItem: ProjectResponse): Boolean = oldItem == newItem
    }
}
